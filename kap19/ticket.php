<?php

declare(strict_types=1);

/**
 * Ein Ticket mit fortlaufender Nummer. Statischer Zähler und Fabrikmethode
 * greifen zusammen: Der Konstruktor ist privat, jede Nummer wird genau
 * einmal vergeben.
 */
final class Ticket
{
    // Klassenweiter Stand der zuletzt vergebenen Nummer.
    private static int $letzteNummer = 0;

    private function __construct(
        public readonly int $nummer,
        public readonly string $titel,
    ) {}

    /**
     * Fabrikmethode: eröffnet ein Ticket mit der nächsten freien Nummer.
     */
    public static function eroeffne(string $titel): self
    {
        // Vorher-Inkrement: erst hochzählen, dann den neuen Stand nehmen.
        return new self(++self::$letzteNummer, $titel);
    }
}

$erstes = Ticket::eroeffne('Login kaputt');
$zweites = Ticket::eroeffne('Tippfehler im Menü');

echo "#{$erstes->nummer}: {$erstes->titel}\n";
echo "#{$zweites->nummer}: {$zweites->titel}\n";
