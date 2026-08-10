<?php

declare(strict_types=1);

namespace App;

/**
 * Rechnet einen Nettobetrag (in Cent) auf den Bruttobetrag hoch.
 * Eine der Klassen, die das Preload-Skript dauerhaft in den Speicher legt.
 */
final class Preisrechner
{
    public function __construct(private readonly float $steuersatz = 0.19) {}

    public function brutto(int $nettoCent): int
    {
        return (int) round($nettoCent * (1.0 + $this->steuersatz));
    }
}
