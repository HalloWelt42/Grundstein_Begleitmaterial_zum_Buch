<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\Application\ZahlungsPort;
use App\Domain\Geld;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Ein Test-Double für den Zahlungs-Port: Es sagt jede Zahlung zu und merkt sich
 * nebenbei, was belastet wurde. So kann ein Test nicht nur das Ergebnis prüfen,
 * sondern auch die Interaktion (wurde genau einmal der richtige Betrag
 * belastet?). Ein schlichter Fake wie in Kapitel 48 - keine Datenbank, kein
 * Netz.
 */
final class ZusagendeZahlung implements ZahlungsPort
{
    /** @var list<Geld> */
    public array $belastungen = [];

    public function belaste(Geld $betrag, string $referenz): void
    {
        $this->belastungen[] = $betrag;
    }
}
