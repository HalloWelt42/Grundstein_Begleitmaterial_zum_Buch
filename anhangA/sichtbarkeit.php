<?php

declare(strict_types=1);

// Asymmetrische Sichtbarkeit (PHP 8.4): von außen lesbar, aber nur
// in der Klasse selbst schreibbar.
final class Temperatur
{
    public function __construct(
        public private(set) float $celsius,
    ) {}

    public function erhoehe(float $delta): void
    {
        $this->celsius += $delta;   // erlaubt: Schreibzugriff aus der Klasse
    }
}

$t = new Temperatur(20.0);
echo "Start:  {$t->celsius}\n";     // Lesen von außen ist erlaubt
$t->erhoehe(2.5);
echo "Danach: {$t->celsius}\n";

// Der Schreibversuch von außen wird zur Laufzeit unterbunden.
try {
    $t->celsius = 99.0;
} catch (\Error $e) {
    echo 'Fehler: ' . $e->getMessage() . "\n";
}
