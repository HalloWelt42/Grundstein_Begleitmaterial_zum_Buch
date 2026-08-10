<?php

declare(strict_types=1);

/*
 * Misst die eine Sache, um die es beim OPcache geht: die Zeit fürs Parsen
 * und Kompilieren einer großen Bibliothek. Das require lädt die Datei,
 * parst sie, kompiliert sie zu Opcodes und deklariert die Klassen - genau
 * diese Arbeit spart der OPcache ab der zweiten Anfrage ein.
 *
 * Voraussetzung: lib.php wurde vorher mit lib-erzeugen.php erzeugt.
 */

$datei = __DIR__ . '/lib.php';

if (!is_file($datei)) {
    fwrite(STDERR, 'lib.php fehlt - bitte zuerst lib-erzeugen.php ausführen.' . PHP_EOL);
    exit(1);
}

$start = hrtime(true);
require $datei;   // parsen, kompilieren, Klassen deklarieren
$dauer = (hrtime(true) - $start) / 1_000_000;

printf('Bibliothek geladen (parsen + kompilieren): %.2f ms%s', $dauer, PHP_EOL);
