<?php

declare(strict_types=1);

namespace App\Vorher;

use DateTimeImmutable;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben (Vorher)
 *
 * Diese Klasse beschafft sich alles selbst: den Zufallscode über
 * random_bytes(), den Ausstellungszeitpunkt über new DateTimeImmutable()
 * und daraus die Ablaufzeit. Genau diese versteckten Abhängigkeiten
 * machen sie praktisch untestbar - ein Test kennt weder den erzeugten
 * Code noch die aktuelle Uhrzeit im Voraus, und istGueltig() hängt an
 * der echten Systemuhr. Es gibt keine Naht, an der ein Test etwas
 * Bekanntes einsetzen könnte.
 */
final class Gutschein
{
    public readonly string $code;

    public readonly DateTimeImmutable $erstelltAm;

    public readonly DateTimeImmutable $gueltigBis;

    public function __construct(public readonly int $wertCent)
    {
        // Versteckte Abhängigkeit 1: der Zufall, direkt hereingeholt.
        $this->code = strtoupper(bin2hex(random_bytes(4)));

        // Versteckte Abhängigkeit 2: die Zeit, direkt hereingeholt.
        $this->erstelltAm = new DateTimeImmutable();
        $this->gueltigBis = $this->erstelltAm->modify('+30 days');
    }

    /*
     * Untestbar: Das Ergebnis hängt an der echten Uhr und fällt bei
     * jedem Lauf anders aus. Ein Test kann den Prüfzeitpunkt nicht
     * festlegen.
     */
    public function istGueltig(): bool
    {
        return new DateTimeImmutable() <= $this->gueltigBis;
    }
}
