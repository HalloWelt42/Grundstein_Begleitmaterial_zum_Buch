<?php

declare(strict_types=1);

/**
 * Ein Temperaturwert, intern immer in Kelvin gespeichert. Der Konstruktor
 * ist privat; erzeugt wird ausschließlich über sprechende Fabrikmethoden.
 */
final class Temperatur
{
    // Typisierte Klassenkonstante (Typ vor dem Namen, seit PHP 8.3).
    private const float NULLPUNKT_KELVIN = 273.15;

    // Privater Konstruktor: von außen nicht mit new aufrufbar.
    private function __construct(
        private readonly float $kelvin,
    ) {}

    /**
     * Fabrikmethode: erzeugt eine Temperatur aus einem Celsius-Wert.
     */
    public static function ausCelsius(float $grad): self
    {
        return new self($grad + self::NULLPUNKT_KELVIN);
    }

    /**
     * Fabrikmethode: erzeugt eine Temperatur aus einem Kelvin-Wert.
     */
    public static function ausKelvin(float $kelvin): self
    {
        return new self($kelvin);
    }

    public function celsius(): float
    {
        return $this->kelvin - self::NULLPUNKT_KELVIN;
    }

    public function kelvin(): float
    {
        return $this->kelvin;
    }
}

$koerper = Temperatur::ausCelsius(37.0);
$gefrierpunkt = Temperatur::ausKelvin(273.15);

// Die Fabrikmethoden lesen sich wie ein Satz und lassen keinen Zweifel,
// in welcher Einheit der übergebene Wert gemeint ist.
printf("Körpertemperatur: %.2f K\n", $koerper->kelvin());
printf("Gefrierpunkt:     %.1f Grad Celsius\n", $gefrierpunkt->celsius());
