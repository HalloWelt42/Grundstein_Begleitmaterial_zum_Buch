<?php

declare(strict_types=1);

namespace App;

use DateInterval;
use Psr\Clock\ClockInterface;

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Stellt Gutscheine aus. Die aktuelle Zeit kommt über den PSR-20-Vertrag
 * ClockInterface herein, nicht über einen direkten Griff zur Systemuhr.
 * Genau diese eine Entkopplung macht das Ausstellen im Test vollständig
 * festnagelbar: In Produktion steckt hinter der Uhr die SystemClock, im
 * Test eine FesteClock mit einem bekannten Zeitpunkt.
 */
final class Gutscheindienst
{
    public function __construct(private readonly ClockInterface $uhr)
    {
    }

    public function stelleAus(int $wertCent, int $gueltigkeitTage = 30): Gutschein
    {
        $jetzt = $this->uhr->now();

        return new Gutschein(
            $wertCent,
            $jetzt,
            // add() gibt ein neues Objekt zurück - $jetzt bleibt unberührt.
            $jetzt->add(new DateInterval("P{$gueltigkeitTage}D")),
        );
    }
}
