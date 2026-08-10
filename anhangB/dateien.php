<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang B: Datei- und Verzeichnisfunktionen
 *
 * Ganze Dateien am Stück, zeilenweises Lesen über einen Stream sowie
 * das Prüfen und Zerlegen von Pfaden. Es wird nur in einem eigenen
 * Ordner im Temp-Verzeichnis gearbeitet. Ausgaben aus echtem 8.4-Lauf.
 */

$dir  = sys_get_temp_dir() . '/grundstein-anhangb';
$pfad = $dir . '/notiz.txt';
@mkdir($dir);

// --- Ganze Datei schreiben und lesen -----------------------------------

file_put_contents($pfad, "erste Zeile\nzweite Zeile\n");
file_put_contents($pfad, "dritte Zeile\n", FILE_APPEND); // anhängen

$inhalt = file_get_contents($pfad);
echo mb_strlen($inhalt), " Zeichen gelesen\n";

// --- Große Dateien: zeilenweise über einen Stream ----------------------

$fh = fopen($pfad, 'r');
$nr = 0;
while (($zeile = fgets($fh)) !== false) {
    $nr++;
    echo "{$nr}: " . rtrim($zeile) . "\n";
}
fclose($fh);

// --- Prüfen und Pfade zerlegen -----------------------------------------

var_dump(is_file($pfad));
var_dump(is_dir($dir));
echo basename($pfad), "\n";
echo pathinfo($pfad, PATHINFO_EXTENSION), "\n";

// --- Aufräumen ---------------------------------------------------------

unlink($pfad);
rmdir($dir);
