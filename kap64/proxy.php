<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\LazyFabrik;
use App\Wetterdienst;

Wetterdienst::$konstruktionen = 0;

// Ein Platzhalter, der beim ersten Zugriff auf das echte Objekt verweist,
// das die Fabrik dann baut und zurückgibt.
$dienst = LazyFabrik::proxy(
    Wetterdienst::class,
    static fn (Wetterdienst $platzhalter): Wetterdienst => new Wetterdienst('Trier'),
);

echo 'Nach dem Bauen:   ' . Wetterdienst::$konstruktionen . " Konstruktionen\n";
echo 'Stadt:            ' . $dienst->stadt() . "\n";
echo 'Nach dem Zugriff: ' . Wetterdienst::$konstruktionen . " Konstruktion(en)\n";
