<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\Clock;
use DateTimeImmutable;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Ein handgeschriebener Stub: Er liefert auf jeden Aufruf von jetzt()
 * denselben, im Konstruktor festgelegten Zeitpunkt. So ist die Zeit im
 * Test eine bekannte Größe - der Test bleibt wiederholbar.
 */
final class FesteClock implements Clock
{
    public function __construct(private readonly DateTimeImmutable $zeitpunkt) {}

    public function jetzt(): DateTimeImmutable
    {
        return $this->zeitpunkt;
    }
}
