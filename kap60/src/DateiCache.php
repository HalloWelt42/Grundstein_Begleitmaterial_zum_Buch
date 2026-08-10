<?php

declare(strict_types=1);

namespace App\Cache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * Ein dateibasierter Cache: jeder Eintrag liegt als eigene Datei im
 * Cache-Verzeichnis und überdauert damit die einzelne Anfrage.
 *
 * Der gespeicherte Wert wird serialisiert, zusammen mit seinem
 * Ablaufzeitpunkt. So kann derselbe Cache auch von einer späteren Anfrage
 * oder einem anderen Prozess gelesen werden - anders als der ArrayCache,
 * dessen Inhalt mit dem Skript verschwindet.
 */
final class DateiCache implements CacheInterface
{
    use PruefeSchluessel;
    use ZeitRechnung;
    use MehrfachOperationen;

    public function __construct(private readonly string $verzeichnis)
    {
        // Das Verzeichnis bei Bedarf anlegen (die dreifache Prüfung fängt
        // den Wettlauf ab, dass ein anderer Prozess es zeitgleich anlegt).
        if (
            !is_dir($this->verzeichnis)
            && !mkdir($this->verzeichnis, 0o775, true)
            && !is_dir($this->verzeichnis)
        ) {
            throw new CacheException(
                "Cache-Verzeichnis \"{$this->verzeichnis}\" lässt sich nicht anlegen."
            );
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->pruefeSchluessel($key);

        $pfad = $this->pfadFuer($key);
        if (!is_file($pfad)) {
            return $default; // Fehltreffer: die Datei gibt es nicht
        }

        $roh = @file_get_contents($pfad);
        if ($roh === false) {
            return $default;
        }

        // Weil wir immer ein Array serialisieren, kann ein gültiger Eintrag
        // nie als false zurückkommen - false heißt also: Datei beschädigt.
        $eintrag = @unserialize($roh);
        if (!is_array($eintrag)) {
            @unlink($pfad);
            return $default;
        }

        if ($this->abgelaufen($eintrag['ablauf'])) {
            @unlink($pfad); // abgelaufen: aufräumen und Fehltreffer melden
            return $default;
        }

        return $eintrag['wert'];
    }

    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $this->pruefeSchluessel($key);

        $eintrag = [
            'ablauf' => $this->ablaufZeitpunkt($ttl),
            'wert'   => $value,
        ];

        // Atomar schreiben: erst in eine eindeutige Nebendatei, dann
        // umbenennen. So sieht ein gleichzeitiger Leser nie einen halben
        // Eintrag - rename() ist auf einem Dateisystem eine atomare Operation.
        $pfad = $this->pfadFuer($key);
        $temp = $pfad . '.' . bin2hex(random_bytes(6)) . '.tmp';

        if (@file_put_contents($temp, serialize($eintrag), LOCK_EX) === false) {
            return false;
        }

        return @rename($temp, $pfad);
    }

    public function delete(string $key): bool
    {
        $this->pruefeSchluessel($key);

        $pfad = $this->pfadFuer($key);
        if (is_file($pfad)) {
            return @unlink($pfad);
        }

        // Einen nicht vorhandenen Schlüssel zu löschen, gilt als Erfolg.
        return true;
    }

    public function clear(): bool
    {
        $erfolg = true;
        foreach (glob($this->verzeichnis . '/*.cache') ?: [] as $datei) {
            $erfolg = @unlink($datei) && $erfolg;
        }

        return $erfolg;
    }

    public function has(string $key): bool
    {
        $this->pruefeSchluessel($key);

        // Ein eindeutiges Wächter-Objekt als Standard: Kommt es zurück, gab
        // es keinen lebenden Eintrag. get() erledigt dabei Ablaufprüfung und
        // das Wegräumen einer beschädigten Datei gleich mit.
        $waechter = new \stdClass();

        return $this->get($key, $waechter) !== $waechter;
    }

    /**
     * Bildet aus dem Schlüssel einen im Dateisystem gültigen Dateinamen
     * fester Länge. Der Hash macht aus jedem erlaubten Schlüssel einen
     * kurzen, kollisionsarmen Namen.
     */
    private function pfadFuer(string $key): string
    {
        return $this->verzeichnis . '/' . hash('sha256', $key) . '.cache';
    }
}
