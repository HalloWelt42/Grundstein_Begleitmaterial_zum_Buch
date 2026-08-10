<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Ein kleiner, selbst geschriebener Leser für Umgebungswerte. Er kennt drei
 * Quellen und eine klare Vorrangfolge (von schwach nach stark):
 *
 *     Standardwerte  <  .env-Datei  <  echte Umgebungsvariablen
 *
 * Die Standardwerte liegen im Code, die .env-Datei trägt die lokalen
 * Entwicklungswerte, und die echten Umgebungsvariablen (etwa aus einem
 * Container) haben immer das letzte Wort. So läuft dieselbe Anwendung in
 * jeder Umgebung, ohne dass eine Zeile Code sich ändert.
 *
 * Env selbst hält nur eine flache Schlüssel-Wert-Abbildung und beantwortet
 * Anfragen darauf. Aus diesen rohen Zeichenketten baut später das typisierte
 * AppConfig-Objekt die eigentliche Konfiguration.
 */
final class Env
{
    /**
     * @param array<string, string> $werte Die fertig zusammengeführten Werte.
     */
    public function __construct(
        private readonly array $werte,
    ) {}

    /**
     * Führt die drei Quellen in der richtigen Vorrangfolge zusammen. Spätere
     * Quellen überschreiben frühere: Die echte Umgebung schlägt die
     * .env-Datei, die .env-Datei schlägt die Standardwerte.
     *
     * @param array<string, string> $standardwerte  Vorgaben aus dem Code.
     * @param string                $envPfad        Pfad zur .env-Datei (darf fehlen).
     * @param array<string, string> $echteUmgebung  meist getenv() oder $_ENV.
     */
    public static function aus(array $standardwerte, string $envPfad, array $echteUmgebung): self
    {
        return new self(array_merge(
            $standardwerte,
            self::parseDatei($envPfad),
            $echteUmgebung,
        ));
    }

    /**
     * Parst eine .env-Datei zu einem Schlüssel-Wert-Array. Robust gegen die
     * üblichen Stolpersteine: Leerzeilen, Kommentarzeilen, umschließende
     * Anführungszeichen, ein optionales führendes "export" und Werte, die
     * selbst ein Gleichheitszeichen enthalten.
     *
     * Fehlt die Datei, ist das kein Fehler: In der Produktion gibt es oft gar
     * keine .env, sondern nur echte Umgebungsvariablen.
     *
     * @return array<string, string>
     */
    public static function parseDatei(string $pfad): array
    {
        if (! is_file($pfad)) {
            return [];
        }

        $werte  = [];
        $zeilen = file($pfad, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($zeilen === false) {
            return [];
        }

        foreach ($zeilen as $zeile) {
            $zeile = trim($zeile);

            // Leerzeile oder ganze Kommentarzeile: überspringen.
            if ($zeile === '' || str_starts_with($zeile, '#')) {
                continue;
            }

            // Optionales "export " am Zeilenanfang (aus Shell-Gewohnheit).
            if (str_starts_with($zeile, 'export ')) {
                $zeile = substr($zeile, 7);
            }

            // Nur am ERSTEN Gleichheitszeichen trennen - ein Wert wie eine DSN
            // darf selbst Gleichheitszeichen enthalten.
            $pos = strpos($zeile, '=');
            if ($pos === false) {
                continue; // Zeile ohne '=' ist kein Schlüssel-Wert-Paar.
            }

            $schluessel = trim(substr($zeile, 0, $pos));
            $wert       = trim(substr($zeile, $pos + 1));

            if ($schluessel === '') {
                continue;
            }

            $werte[$schluessel] = self::entferneAnfuehrung($wert);
        }

        return $werte;
    }

    /**
     * Liest einen Wert oder gibt den Standardwert zurück, wenn er fehlt.
     */
    public function hole(string $schluessel, ?string $standard = null): ?string
    {
        return $this->werte[$schluessel] ?? $standard;
    }

    /**
     * Liest einen Pflichtwert. Fehlt er (oder ist leer), bricht die
     * Konfiguration mit einer klaren Ausnahme ab - lieber sofort und laut als
     * später und leise mit einem halb gebauten System.
     */
    public function pflicht(string $schluessel): string
    {
        $wert = $this->werte[$schluessel] ?? '';

        if ($wert === '') {
            throw new ConfigFehler("Pflichtwert {$schluessel} fehlt in der Konfiguration.");
        }

        return $wert;
    }

    /**
     * Liest einen Wahrheitswert aus einer Zeichenkette. Umgebungsvariablen sind
     * immer Zeichenketten, also braucht "true"/"1"/"on"/"yes" eine bewusste
     * Umdeutung; alles andere gilt als false.
     */
    public function bool(string $schluessel, bool $standard = false): bool
    {
        $wert = $this->werte[$schluessel] ?? null;

        if ($wert === null) {
            return $standard;
        }

        return in_array(strtolower(trim($wert)), ['1', 'true', 'on', 'yes'], true);
    }

    /**
     * Streift genau ein Paar umschließender Anführungszeichen ab - doppelte
     * wie einfache. Anführungszeichen im Inneren bleiben unangetastet.
     */
    private static function entferneAnfuehrung(string $wert): string
    {
        if (strlen($wert) < 2) {
            return $wert;
        }

        $erstes  = $wert[0];
        $letztes = $wert[strlen($wert) - 1];

        $doppelt = $erstes === '"' && $letztes === '"';
        $einfach = $erstes === "'" && $letztes === "'";

        return $doppelt || $einfach ? substr($wert, 1, -1) : $wert;
    }
}
