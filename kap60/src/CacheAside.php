<?php

declare(strict_types=1);

namespace App\Cache;

use Psr\SimpleCache\CacheInterface;

/**
 * Gießt das Muster Cache-aside in eine kleine Hilfsklasse: nachschlagen,
 * bei einem Fehltreffer berechnen und ablegen, das Ergebnis zurückgeben.
 *
 * Die Klasse hängt allein am PSR-16-Vertrag und umgeht die null-Falle des
 * naiven Cache-aside mit einem Wächter-Objekt: Nur wenn genau dieses
 * eindeutige Objekt zurückkommt, war es wirklich ein Fehltreffer - ein
 * gespeicherter Wert null wird korrekt als Treffer erkannt.
 */
final class CacheAside
{
    public function __construct(private readonly CacheInterface $cache)
    {
    }

    /**
     * @template T
     * @param callable(): T $berechne
     * @return T
     */
    public function holeOderBerechne(string $schluessel, int $ttl, callable $berechne): mixed
    {
        // Ein eindeutiges Wächter-Objekt als Standard. Kommt es zurück, war
        // es ein echter Fehltreffer - und nicht ein gespeicherter Wert null.
        $waechter = new \stdClass();

        $wert = $this->cache->get($schluessel, $waechter);
        if ($wert !== $waechter) {
            return $wert; // Treffer
        }

        $wert = $berechne();                         // Fehltreffer: teuer rechnen
        $this->cache->set($schluessel, $wert, $ttl); // und fürs nächste Mal ablegen

        return $wert;
    }
}
