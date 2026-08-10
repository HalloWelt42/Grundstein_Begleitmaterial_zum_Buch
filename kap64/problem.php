<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Wetterdienst;

// Das Problem: Wir bauen drei Dienste im Voraus - aber nur einen brauchen
// wir am Ende wirklich. Jeder Konstruktor macht seine teure Arbeit trotzdem.
Wetterdienst::$konstruktionen = 0;

$dienste = [
    'Lüneburg'  => new Wetterdienst('Lüneburg'),
    'Buxtehude' => new Wetterdienst('Buxtehude'),
    'Kiel'      => new Wetterdienst('Kiel'),
];

// Genutzt wird nur ein einziger davon.
echo 'Temperatur in Kiel: ' . $dienste['Kiel']->temperatur() . " Grad\n";
echo 'Teure Konstruktionen: ' . Wetterdienst::$konstruktionen . "\n";
