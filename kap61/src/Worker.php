<?php

declare(strict_types=1);

namespace App;

use Closure;
use RuntimeException;
use Throwable;

/**
 * Ein lange laufender Worker, der Aufträge aus der Queue abarbeitet.
 *
 * Der Worker weiß selbst nicht, was ein Auftrag bedeutet - das sagen ihm
 * Handler, die man je Auftragstyp registriert. Sein Kern ist eine
 * Schleife: Auftrag holen, bearbeiten, wiederholen. Ein Feld steuert das
 * saubere Beenden: halteAn() setzt es, die Schleife liest es an ihrer
 * Bedingung. So kann ein Signal-Handler den Worker bitten anzuhalten,
 * ohne den gerade laufenden Auftrag abzuschneiden.
 */
final class Worker
{
    /**
     * Registrierte Handler: Auftragstyp -> Closure, die die Arbeit tut.
     *
     * @var array<string, Closure(Auftrag): void>
     */
    private array $handler = [];

    /** Steuert die Schleife; halteAn() setzt es auf false. */
    private bool $laeuftWeiter = true;

    /**
     * @param Queue                 $queue           Die Warteschlange.
     * @param Closure(string): void $protokoll       Nimmt Protokollzeilen entgegen.
     * @param int                   $leerlaufPause   Sekunden Warten bei leerer Queue.
     * @param bool                  $endeBeiLeerlauf Bei leerer Queue enden statt warten?
     */
    public function __construct(
        private readonly Queue $queue,
        private readonly Closure $protokoll,
        private readonly int $leerlaufPause = 1,
        private readonly bool $endeBeiLeerlauf = false,
    ) {}

    /**
     * Registriert einen Handler für einen Auftragstyp.
     *
     * @param Closure(Auftrag): void $handler
     */
    public function registriere(string $typ, Closure $handler): void
    {
        $this->handler[$typ] = $handler;
    }

    /**
     * Startet die Arbeitsschleife und liefert die Zahl bearbeiteter Aufträge.
     */
    public function starte(): int
    {
        $verarbeitet = 0;

        while ($this->laeuftWeiter) {
            $auftrag = $this->queue->reserviere();

            if ($auftrag === null) {
                if ($this->endeBeiLeerlauf) {
                    break; // Nichts mehr zu tun - Runde beenden.
                }

                // Warten und danach die Schleifenbedingung erneut prüfen;
                // ein zwischenzeitliches Signal beendet uns dann sauber.
                sleep($this->leerlaufPause);

                continue;
            }

            $this->bearbeite($auftrag);
            ++$verarbeitet;
        }

        return $verarbeitet;
    }

    /**
     * Bittet den Worker, nach dem aktuellen Auftrag anzuhalten.
     *
     * Genau das ruft der Signal-Handler auf. Der Worker kennt weder
     * Signale noch pcntl - er sieht nur dieses Flag.
     */
    public function halteAn(): void
    {
        $this->laeuftWeiter = false;
    }

    /**
     * Führt einen einzelnen Auftrag aus und fängt jeden Fehler.
     *
     * Ein kaputter Auftrag darf den Worker niemals zum Absturz bringen:
     * Jeder Fehler wird gefangen, an die Queue gemeldet und protokolliert;
     * der Worker selbst dreht unbeirrt weiter.
     */
    private function bearbeite(Auftrag $auftrag): void
    {
        try {
            $handler = $this->handler[$auftrag->typ]
                ?? throw new RuntimeException("Kein Handler für Typ '{$auftrag->typ}'.");

            ($handler)($auftrag);
            $this->queue->erledige($auftrag->id);
            ($this->protokoll)("Auftrag #{$auftrag->id} ({$auftrag->typ}) erledigt.");
        } catch (Throwable $fehler) {
            $this->queue->meldeFehler($auftrag->id, $fehler->getMessage());
            ($this->protokoll)(sprintf(
                'Auftrag #%d (%s) fehlgeschlagen bei Versuch %d: %s',
                $auftrag->id,
                $auftrag->typ,
                $auftrag->versuch,
                $fehler->getMessage(),
            ));
        }
    }
}
