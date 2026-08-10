<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\Clock;
use DateTimeImmutable;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben
 *
 * Ein handgeschriebener Stub für die Uhr: Er liefert auf jeden Aufruf
 * von jetzt() denselben, im Konstruktor festgelegten Zeitpunkt. So wird
 * die Zeit im Test zu einer bekannten Größe.
 */
final class FesteClock implements Clock
{
    public function __construct(private readonly DateTimeImmutable $zeitpunkt) {}

    public function jetzt(): DateTimeImmutable
    {
        return $this->zeitpunkt;
    }
}
