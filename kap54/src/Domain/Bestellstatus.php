<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Der Lebenszyklus einer Bestellung als wertbehaftetes Enum. Ein Status ist
 * keine beliebige Zeichenkette mehr, sondern einer aus genau drei erlaubten
 * Werten - falsche Zustände sind damit ausgeschlossen.
 */
enum Bestellstatus: string
{
    case Offen = 'offen';
    case Bezahlt = 'bezahlt';
    case Storniert = 'storniert';
}
