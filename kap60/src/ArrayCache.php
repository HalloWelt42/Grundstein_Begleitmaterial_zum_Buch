<?php

declare(strict_types=1);

namespace App\Cache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * Ein einfacher Cache, der seine Einträge nur im Arbeitsspeicher hält.
 *
 * Er lebt genau so lange wie die aktuelle Anfrage: Endet das Skript, ist der
 * ganze Inhalt weg. Das macht ihn zum idealen Ergebnis-Cache innerhalb einer
 * Anfrage - und zur perfekten, weil sofort verfügbaren Umsetzung, um den
 * PSR-16-Vertrag zu verstehen.
 */
final class ArrayCache implements CacheInterface
{
    use PruefeSchluessel;
    use ZeitRechnung;
    use MehrfachOperationen;

    /** @var array<string, array{ablauf: int|null, wert: mixed}> */
    private array $eintraege = [];

    public function get(string $key, mixed $default = null): mixed
    {
        $this->pruefeSchluessel($key);

        if (!isset($this->eintraege[$key])) {
            return $default; // Fehltreffer
        }

        $eintrag = $this->eintraege[$key];
        if ($this->abgelaufen($eintrag['ablauf'])) {
            unset($this->eintraege[$key]); // abgelaufenen Eintrag entsorgen
            return $default;
        }

        return $eintrag['wert']; // Treffer
    }

    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $this->pruefeSchluessel($key);

        $this->eintraege[$key] = [
            'ablauf' => $this->ablaufZeitpunkt($ttl),
            'wert'   => $value,
        ];

        return true;
    }

    public function delete(string $key): bool
    {
        $this->pruefeSchluessel($key);
        unset($this->eintraege[$key]);

        return true;
    }

    public function clear(): bool
    {
        $this->eintraege = [];

        return true;
    }

    public function has(string $key): bool
    {
        $this->pruefeSchluessel($key);

        if (!isset($this->eintraege[$key])) {
            return false;
        }

        if ($this->abgelaufen($this->eintraege[$key]['ablauf'])) {
            unset($this->eintraege[$key]);
            return false;
        }

        return true;
    }
}
