<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\BestellungBezahlt;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Der dritte Zuhörer: Er schreibt dem Kunden Treuepunkte gut - einen Punkt je
 * vollem Euro der bezahlten Bestellung. Als eigenständiger Baustein lässt er
 * sich jederzeit anmelden oder abmelden, ohne dass eine andere Zeile der
 * Anwendung sich ändert.
 */
final class Treuepunkte
{
    /** @var array<string, int> Gesammelte Punkte je Kunde. */
    private array $punkte = [];

    public function __invoke(BestellungBezahlt $ereignis): void
    {
        // Ein Treuepunkt je vollem Euro (ganzzahlig, keine Bruchteile).
        $verdient = intdiv($ereignis->betrag->cent, 100);

        $this->punkte[$ereignis->kunde] = ($this->punkte[$ereignis->kunde] ?? 0) + $verdient;
    }

    public function fuer(string $kunde): int
    {
        return $this->punkte[$kunde] ?? 0;
    }
}
