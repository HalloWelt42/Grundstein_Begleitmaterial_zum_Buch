<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\LazyFabrik;
use App\Wetterdienst;

Wetterdienst::$konstruktionen = 0;

// Drei Platzhalter bauen - noch läuft KEIN Konstruktor.
$dienste = [];
foreach (['Lüneburg', 'Buxtehude', 'Kiel'] as $stadt) {
    $dienste[$stadt] = LazyFabrik::ghost(
        Wetterdienst::class,
        // Der Initialisierer füllt später genau dieses Objekt,
        // indem er seinen eigenen Konstruktor aufruft.
        static function (Wetterdienst $dienst) use ($stadt): void {
            $dienst->__construct($stadt);
        },
    );
}

echo 'Nach dem Bauen:   ' . Wetterdienst::$konstruktionen . " Konstruktionen\n";

// Erst dieser eine Zugriff löst die Initialisierung genau eines Dienstes aus.
echo 'Temperatur Kiel:  ' . $dienste['Kiel']->temperatur() . " Grad\n";
echo 'Nach dem Zugriff: ' . Wetterdienst::$konstruktionen . " Konstruktion(en)\n";
