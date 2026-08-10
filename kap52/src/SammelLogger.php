<?php

declare(strict_types=1);

namespace App;

/**
 * Ein Logger, der jede Zeile im Speicher sammelt, statt sie sofort
 * auszugeben. Praktisch für die Ausgabe am Ende und für Tests, weil sich
 * das Gesammelte hinterher Zeile für Zeile prüfen lässt.
 */
final class SammelLogger implements Logger
{
    /** @var list<string> Alle bisher notierten Zeilen in ihrer Reihenfolge. */
    private array $zeilen = [];

    public function notiere(string $zeile): void
    {
        $this->zeilen[] = $zeile;
    }

    /**
     * Gibt alle gesammelten Zeilen zurück.
     *
     * @return list<string>
     */
    public function alleZeilen(): array
    {
        return $this->zeilen;
    }
}
