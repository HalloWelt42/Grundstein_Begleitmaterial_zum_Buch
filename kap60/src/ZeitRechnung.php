<?php

declare(strict_types=1);

namespace App\Cache;

use DateInterval;
use DateTimeImmutable;

/**
 * Rechnet die drei erlaubten TTL-Formen von PSR-16 in einen absoluten
 * Ablaufzeitpunkt um und prüft, ob dieser erreicht ist.
 *
 * PSR-16 erlaubt als Lebensdauer (Time To Live): null (kein Ablauf, so lange
 * wie möglich speichern), eine ganze Zahl von Sekunden oder ein DateInterval.
 */
trait ZeitRechnung
{
    /**
     * Wandelt eine TTL-Angabe in einen absoluten Ablaufzeitpunkt als
     * Unix-Zeit um. null bedeutet: läuft nie ab.
     */
    private function ablaufZeitpunkt(null|int|DateInterval $ttl): ?int
    {
        if ($ttl === null) {
            return null;
        }

        // Ein DateInterval wird über einen konkreten Zeitpunkt in Sekunden
        // umgerechnet - so werden auch Monate und Jahre korrekt berücksichtigt.
        $sekunden = $ttl instanceof DateInterval
            ? (new DateTimeImmutable())->add($ttl)->getTimestamp() - time()
            : $ttl;

        return time() + $sekunden;
    }

    /**
     * Ist der Ablaufzeitpunkt erreicht? Ein Eintrag ohne Ablauf (null) gilt
     * nie als abgelaufen.
     */
    private function abgelaufen(?int $ablauf): bool
    {
        return $ablauf !== null && $ablauf <= time();
    }
}
