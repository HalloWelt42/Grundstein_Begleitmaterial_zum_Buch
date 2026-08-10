<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use InvalidArgumentException;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein typisiertes Konfigurationsobjekt (Kapitel 57). Statt überall im Code
 * roh in $_ENV oder getenv() zu greifen, wandern alle Einstellungen einmal
 * an einer Stelle in ein unveränderliches Objekt mit festen Typen. Der Rest
 * der Anwendung liest nur noch fertige, geprüfte Werte - kein string mehr,
 * wo eine Umgebung gemeint ist.
 */
final readonly class Config
{
    public function __construct(
        public string $appName,
        public Umgebung $umgebung,
        public string $datenbankDsn,
    ) {}

    /**
     * Baut das Config-Objekt aus rohen Schlüssel-Wert-Paaren, wie sie aus der
     * Umgebungsdatei oder den Prozess-Umgebungsvariablen kommen. Fehlende
     * Werte bekommen einen sinnvollen Standard; ein unbekannter Umgebungsname
     * ist ein harter Fehler - lieber früh und laut als später und still.
     *
     * @param array<string, string> $werte
     */
    public static function ausWerten(array $werte): self
    {
        $umgebungsname = $werte['APP_UMGEBUNG'] ?? Umgebung::Entwicklung->value;
        $umgebung = Umgebung::tryFrom($umgebungsname);

        if ($umgebung === null) {
            throw new InvalidArgumentException(
                "Unbekannte Umgebung: '{$umgebungsname}'. Erlaubt sind "
                . "'entwicklung' und 'produktion'."
            );
        }

        return new self(
            appName: $werte['APP_NAME'] ?? 'Grundstein',
            umgebung: $umgebung,
            datenbankDsn: $werte['DB_DSN'] ?? 'sqlite::memory:',
        );
    }

    public function istEntwicklung(): bool
    {
        return $this->umgebung === Umgebung::Entwicklung;
    }
}
