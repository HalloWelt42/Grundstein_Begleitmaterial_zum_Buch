<?php

declare(strict_types=1);

namespace App;

/**
 * Eine absichtlich winzige Anwendung. Im Deployment-Kapitel geht es nicht
 * um ihre Fachlichkeit, sondern darum, sie sicher und wiederholbar in den
 * Betrieb zu bringen. Sie meldet nur, welche Version gerade läuft und in
 * welcher Umgebung - genau das brauchen wir, um beim Zero-Downtime-Umschalten
 * zu sehen, welche Fassung eine Anfrage tatsächlich beantwortet hat.
 */
final class Application
{
    public function __construct(
        private readonly string $version,
        private readonly string $umgebung,
    ) {
    }

    /**
     * Beantwortet eine Anfrage anhand ihres Pfads. Bewusst schlicht - ein
     * echtes Routing steckt in Kapitel 43.
     *
     * @return array{code: int, text: string}
     */
    public function beantworte(string $pfad): array
    {
        return match ($pfad) {
            '/' => [
                'code' => 200,
                'text' => sprintf(
                    'Grundstein läuft. Version %s, Umgebung %s.',
                    $this->version,
                    $this->umgebung,
                ),
            ],
            default => [
                'code' => 404,
                'text' => "Nicht gefunden: {$pfad}",
            ],
        };
    }
}
