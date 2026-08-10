<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Die Testuhr. Sie liefert immer denselben, im Konstruktor festgelegten
 * Zeitpunkt und macht damit jede zeitabhängige Logik wiederholbar. An
 * dieser Naht ersetzt sie im Test die echte SystemClock, ohne dass der
 * abhängige Code etwas davon merkt - beide erfüllen ClockInterface.
 */
final class FesteClock implements ClockInterface
{
    public function __construct(private readonly DateTimeImmutable $zeitpunkt)
    {
    }

    public function now(): DateTimeImmutable
    {
        return $this->zeitpunkt;
    }
}
