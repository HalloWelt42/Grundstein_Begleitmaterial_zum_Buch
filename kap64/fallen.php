<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\LazyFabrik;
use App\Wetterdienst;

// Ein kleiner Helfer, damit die Fallen kurz bleiben: baut einen Ghost für
// eine Stadt.
$ghostFuer = static fn (string $stadt): Wetterdienst => LazyFabrik::ghost(
    Wetterdienst::class,
    static function (Wetterdienst $dienst) use ($stadt): void {
        $dienst->__construct($stadt);
    },
);

// Falle 1 - Identität: Ein Proxy und sein echtes Objekt sind ZWEI Objekte.
$reales = null;
$proxy = LazyFabrik::proxy(
    Wetterdienst::class,
    static function (Wetterdienst $platzhalter) use (&$reales): Wetterdienst {
        $reales = new Wetterdienst('Kiel');

        return $reales;
    },
);
$proxy->stadt(); // Zugriff löst die Fabrik aus, $reales ist nun gesetzt.
echo 'Proxy === reales Objekt? '
    . ($proxy === $reales ? 'ja' : 'nein') . "\n";

// Falle 2 - Wertvergleich materialisiert: == baut beide Seiten auf.
Wetterdienst::$konstruktionen = 0;
$a = $ghostFuer('Trier');
$b = $ghostFuer('Trier');
echo 'Vor ==:  ' . Wetterdienst::$konstruktionen . " Konstruktionen\n";
$gleich = ($a == $b);
echo 'Nach ==: ' . Wetterdienst::$konstruktionen
    . ' Konstruktionen, gleich=' . ($gleich ? 'ja' : 'nein') . "\n";

// Falle 3 - Serialisierung materialisiert ebenfalls.
Wetterdienst::$konstruktionen = 0;
$c = $ghostFuer('Hamburg');
$roh = serialize($c);
echo 'Nach serialize: ' . Wetterdienst::$konstruktionen . " Konstruktion(en)\n";
