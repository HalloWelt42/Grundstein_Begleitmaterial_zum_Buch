<?php

declare(strict_types=1);

use App\Cache\ArrayCache;

require __DIR__ . '/vendor/autoload.php';

$cache = new ArrayCache();

// --- Strategie 1: Ablauf durch Lebensdauer (TTL) ---------------------
$cache->set('wetter.buxtehude', '18 Grad, wolkig', ttl: 1);
echo 'Direkt nach set(): ' . var_export($cache->has('wetter.buxtehude'), true) . PHP_EOL;

sleep(2); // die Lebensdauer von einer Sekunde verstreichen lassen
echo 'Zwei Sekunden später: ' . var_export($cache->has('wetter.buxtehude'), true) . PHP_EOL;
echo 'get() liefert jetzt den Standard: '
    . var_export($cache->get('wetter.buxtehude', 'unbekannt'), true) . PHP_EOL;

echo PHP_EOL;

// --- Strategie 3: Versionierung im Schlüssel -------------------------
$version = 7; // steigt bei jeder Änderung an den Stammdaten
$cache->set("stammdaten.v{$version}", 'Datensatz in Fassung 7');

// Nach einer Änderung zählt die Version hoch - der alte Schlüssel wird nie
// wieder gelesen und verfällt von selbst über seine TTL.
$version = 8;
echo 'Neuer Schlüssel, noch kein Eintrag: '
    . $cache->get("stammdaten.v{$version}", 'Fehltreffer') . PHP_EOL;
