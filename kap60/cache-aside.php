<?php

declare(strict_types=1);

use App\Cache\DateiCache;
use App\Cache\StatistikDienst;

require __DIR__ . '/vendor/autoload.php';

$cache  = new DateiCache(__DIR__ . '/var/cache');
$dienst = new StatistikDienst();

// Für ein reproduzierbares Beispiel mit leerem Cache starten.
$cache->clear();

$schluessel = 'primzahlen.summe.2000000';

// Dieselbe Anfrage dreimal - der erste Aufruf muss rechnen, die weiteren
// holen das Ergebnis aus dem Cache.
for ($versuch = 1; $versuch <= 3; $versuch++) {
    $start = microtime(true);

    // Cache-aside von Hand: erst nachschlagen ...
    $summe = $cache->get($schluessel);
    if ($summe === null) {
        // ... bei einem Fehltreffer berechnen und ablegen.
        $summe = $dienst->summeDerPrimzahlenBis(2_000_000);
        $cache->set($schluessel, $summe, 3600); // eine Stunde gültig
        $herkunft = 'berechnet    ';
    } else {
        $herkunft = 'aus dem Cache';
    }

    $dauer = (microtime(true) - $start) * 1000.0;
    printf("Versuch %d: %s -> Summe %d (%6.2f ms)\n", $versuch, $herkunft, $summe, $dauer);
}

printf("Der Dienst hat insgesamt %dx wirklich gerechnet.\n", $dienst->anzahlBerechnungen());
