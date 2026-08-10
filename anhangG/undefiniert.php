<?php

declare(strict_types=1);

// Seit PHP 8.0 ist der Zugriff auf einen fehlenden Array-Schlüssel eine
// Warnung (früher nur ein leiser Notice) - der Wert ist dann null.

$konfig = ['host' => 'localhost'];

// Fehler (Warnung): der Schlüssel 'port' existiert nicht.
$port = $konfig['port'];
echo "Port ungeprüft: '{$port}'\n";

// Korrektur: Null-Koaleszenz liefert einen Ersatz, ganz ohne Warnung.
$portSicher = $konfig['port'] ?? 8080;
echo "Port sicher: {$portSicher}\n";

// Der Aufruf einer nicht vorhandenen Funktion ist ein Error - anders als
// eine Warnung bricht er ab, lässt sich aber fangen.
try {
    $summe = array_sum_all([1, 2, 3]);   // diese Funktion gibt es nicht
} catch (\Error $fehler) {
    echo 'Error: ' . $fehler->getMessage() . "\n";
}
