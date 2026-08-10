<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Clock;
use DateTimeImmutable;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Infrastruktur-Adapter für die Uhr. Der einzige erlaubte Ort, an dem
 * new DateTimeImmutable() noch auftaucht - sauber hinter dem Clock-Vertrag
 * gekapselt (Humble Object aus Kapitel 49).
 */
final class SystemClock implements Clock
{
    public function jetzt(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
