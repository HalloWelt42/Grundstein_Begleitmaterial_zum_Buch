<?php

declare(strict_types=1);

namespace App;

/**
 * Ein absichtlich teurer Dienst. Sein Konstruktor steht stellvertretend
 * für einen Aufbau, der ein Netz oder eine Datenbank befragt - Arbeit,
 * die man nur dann bezahlen will, wenn der Dienst wirklich gebraucht wird.
 */
final class Wetterdienst
{
    /** Zählt mit, wie oft der teure Konstruktor tatsächlich lief. */
    public static int $konstruktionen = 0;

    /** @var array<string, float> Die (teuer beschafften) Messwerte. */
    private array $messwerte;

    public function __construct(private readonly string $stadt)
    {
        // Hier stünde der teure Zugriff aufs Netz oder die Datenbank.
        // Wir zählen ihn nur mit, damit Demos und Tests beweisen können,
        // WANN er wirklich stattfindet.
        self::$konstruktionen++;
        $this->messwerte = ['temperatur' => 21.5, 'wind' => 12.0];
    }

    public function stadt(): string
    {
        return $this->stadt;
    }

    public function temperatur(): float
    {
        return $this->messwerte['temperatur'];
    }
}
