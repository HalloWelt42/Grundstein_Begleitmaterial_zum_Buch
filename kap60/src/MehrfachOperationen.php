<?php

declare(strict_types=1);

namespace App\Cache;

use DateInterval;

/**
 * Setzt die vier Mehrfach-Methoden von PSR-16 auf die Einzeloperationen
 * get(), set() und delete() zurück. Jede Cache-Klasse, die diese drei
 * Methoden mitbringt, bekommt getMultiple(), setMultiple() und
 * deleteMultiple() so kostenlos und einheitlich dazu.
 */
trait MehrfachOperationen
{
    /**
     * @param iterable<string> $keys
     * @return iterable<string, mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $ergebnis = [];
        foreach ($keys as $key) {
            $ergebnis[$key] = $this->get($key, $default);
        }

        return $ergebnis;
    }

    /**
     * @param iterable<string, mixed> $values
     */
    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        $erfolg = true;
        foreach ($values as $key => $value) {
            // && rechts, damit jede Operation wirklich ausgeführt wird.
            $erfolg = $this->set((string) $key, $value, $ttl) && $erfolg;
        }

        return $erfolg;
    }

    /**
     * @param iterable<string> $keys
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $erfolg = true;
        foreach ($keys as $key) {
            $erfolg = $this->delete($key) && $erfolg;
        }

        return $erfolg;
    }
}
