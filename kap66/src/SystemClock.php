<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;
use DateTimeZone;
use Psr\Clock\ClockInterface;

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Die Betriebsuhr. Sie erfüllt den PSR-20-Vertrag ClockInterface und ist
 * der einzige erlaubte Ort, an dem "new DateTimeImmutable('now')" noch
 * auftaucht - sauber gekapselt hinter einer Naht (Kapitel 49). Die Zeit
 * kommt bewusst in UTC heraus: gespeichert und gerechnet wird immer in
 * UTC, die lokale Zone dient nur der Anzeige (die goldene Regel).
 */
final class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
