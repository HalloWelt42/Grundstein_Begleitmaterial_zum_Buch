<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\Application\Clock;
use DateTimeImmutable;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Test-Double)
 *
 * Eine Uhr, die immer denselben Zeitpunkt liefert (Stub aus Kapitel 49).
 * Damit ist der Anmeldezeitpunkt im Test im Voraus bekannt und exakt
 * prüfbar.
 */
final class FesteClock implements Clock
{
    public function __construct(
        private readonly DateTimeImmutable $zeitpunkt,
    ) {}

    public function jetzt(): DateTimeImmutable
    {
        return $this->zeitpunkt;
    }
}
