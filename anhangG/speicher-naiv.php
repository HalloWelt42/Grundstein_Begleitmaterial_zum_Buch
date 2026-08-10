<?php

declare(strict_types=1);

// Bewusst falsch, um den Fehler zu zeigen: Der Aufbau hält Millionen Werte
// gleichzeitig im Speicher. Das enge Limit löst den Abbruch zuverlässig aus -
// im Betrieb setzt die Grenze die php.ini.
ini_set('memory_limit', '32M');

$alle = range(1, 100_000_000);   // alle Werte auf einmal - zu viel
$summe = array_sum($alle);

echo 'Summe: ' . $summe . "\n";
