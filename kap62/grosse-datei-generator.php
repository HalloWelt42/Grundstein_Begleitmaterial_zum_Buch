<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Textdatei;

/*
 * Grundstein - Kapitel 62: Eine große Datei Zeile für Zeile lesen
 *
 * Wir erzeugen eine große Textdatei und zählen darin die Fehlerzeilen -
 * einmal, indem wir die ganze Datei mit file() in ein Array laden, und
 * einmal Zeile für Zeile über einen Generator. Der Spitzenspeicher zeigt
 * den Unterschied deutlich.
 *
 * Aufruf:
 *   php grosse-datei-generator.php array       (ganze Datei mit file())
 *   php grosse-datei-generator.php generator   (Zeile für Zeile)
 */

$modus      = $argv[1] ?? 'generator';
$zeilenZahl = 1_000_000;
$pfad       = __DIR__ . '/grosse-datei.tmp';

// --- Die große Datei zur Demonstration anlegen -----------------------
$griff = fopen($pfad, 'wb');
if ($griff === false) {
    fwrite(STDERR, "Kann {$pfad} nicht anlegen." . PHP_EOL);
    exit(1);
}
for ($i = 1; $i <= $zeilenZahl; $i++) {
    // Jede zehnte Zeile ist ein "FEHLER", der Rest "OK".
    $art = $i % 10 === 0 ? 'FEHLER' : 'OK';
    fwrite($griff, "Zeile {$i}: {$art}" . PHP_EOL);
}
fclose($griff);

printf('Dateigröße:      %.1f MB' . PHP_EOL, filesize($pfad) / 1024 / 1024);

// --- Die Fehlerzeilen zählen -----------------------------------------
$fehler  = 0;
$gelesen = 0;

if ($modus === 'array') {
    // file() liest die GANZE Datei in ein Array aus Zeilen - der Speicher
    // wächst mit der Dateigröße.
    $zeilen = file($pfad, FILE_IGNORE_NEW_LINES);
    foreach ($zeilen as $zeile) {
        $gelesen++;
        if (str_contains($zeile, 'FEHLER')) {
            $fehler++;
        }
    }
} else {
    // Der Generator hält immer nur die eine Zeile, die gerade drankommt -
    // der Speicher bleibt flach, egal wie groß die Datei ist.
    foreach (Textdatei::zeilen($pfad) as $zeile) {
        $gelesen++;
        if (str_contains($zeile, 'FEHLER')) {
            $fehler++;
        }
    }
}

printf('Modus:           %s' . PHP_EOL, $modus);
printf('Zeilen gelesen:  %s' . PHP_EOL, number_format($gelesen, 0, ',', '.'));
printf('Fehlerzeilen:    %s' . PHP_EOL, number_format($fehler, 0, ',', '.'));
printf('Spitzenspeicher: %.2f MB' . PHP_EOL, memory_get_peak_usage() / 1024 / 1024);

// Aufräumen - die große Datei war nur für die Demonstration da.
unlink($pfad);
