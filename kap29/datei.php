<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 29: Dateien, Streams und JSON
 *
 * Teil 1: Eine Datei als Ganzes lesen und schreiben, dann häppchenweise
 * mit fopen/fgets/fwrite/fclose für große Dateien, ein kurzer Blick auf
 * das Stream-Konzept (php://temp) und saubere Fehlerbehandlung.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

// Wir legen alle Beispieldateien in einem temporären Verzeichnis ab,
// damit der Lauf reproduzierbar bleibt und nichts liegen bleibt.
$verzeichnis = sys_get_temp_dir() . '/grundstein-kap29';
if (!is_dir($verzeichnis)) {
    mkdir($verzeichnis, 0755, true);
}

/*
 * ---------------------------------------------------------------
 * 1. Eine Datei als Ganzes: file_put_contents und file_get_contents
 * ---------------------------------------------------------------
 */

$pfad = $verzeichnis . '/notiz.txt';
$text = "Erste Zeile\nZweite Zeile mit Umlauten: ä ö ü ß\nDritte Zeile\n";

// file_put_contents schreibt den ganzen String auf einen Schlag und
// gibt die Zahl der geschriebenen Bytes zurück - oder false bei Fehler.
$bytes = file_put_contents($pfad, $text);
if ($bytes === false) {
    // Rückgabe prüfen, nie blind vertrauen.
    fwrite(STDERR, "Konnte {$pfad} nicht schreiben.\n");
    exit(1);
}
echo "Geschrieben: {$bytes} Bytes." . PHP_EOL;

// file_get_contents liest die ganze Datei in einen String.
$inhalt = file_get_contents($pfad);
if ($inhalt === false) {
    fwrite(STDERR, "Konnte {$pfad} nicht lesen.\n");
    exit(1);
}
echo 'Anzahl Zeichen (mb): ' . mb_strlen($inhalt) . PHP_EOL;

// file() liest die Datei gleich als Array von Zeilen. Das Flag
// FILE_IGNORE_NEW_LINES entfernt die Zeilenumbrüche am Ende.
$zeilen = file($pfad, FILE_IGNORE_NEW_LINES);
echo 'Anzahl Zeilen: ' . count($zeilen) . PHP_EOL;

/*
 * ---------------------------------------------------------------
 * 2. Anhängen statt überschreiben mit dem Flag FILE_APPEND
 * ---------------------------------------------------------------
 */

file_put_contents($pfad, "Vierte Zeile, angehängt\n", FILE_APPEND);
echo 'Zeilen nach dem Anhängen: ' . count(file($pfad)) . PHP_EOL;

/*
 * ---------------------------------------------------------------
 * 3. Häppchenweise lesen mit fopen/fgets/fclose
 * ---------------------------------------------------------------
 *
 * Bei großen Dateien will man nicht alles auf einmal in den Speicher
 * laden. Man öffnet einen Datei-Zeiger (Handle) und liest Zeile für
 * Zeile. Der Modus 'r' bedeutet: nur lesen, Zeiger am Anfang.
 */

$zeiger = fopen($pfad, 'r');
if ($zeiger === false) {
    fwrite(STDERR, "Konnte {$pfad} nicht öffnen.\n");
    exit(1);
}

$nummer = 0;
// fgets liefert die nächste Zeile oder false am Dateiende.
while (($zeile = fgets($zeiger)) !== false) {
    $nummer++;
    // rtrim entfernt nur den Zeilenumbruch am Ende.
    echo "Zeile {$nummer}: " . rtrim($zeile, "\n") . PHP_EOL;
}
// Den Zeiger immer schließen, wenn er nicht mehr gebraucht wird.
fclose($zeiger);

/*
 * ---------------------------------------------------------------
 * 4. Häppchenweise schreiben mit fopen/fwrite/fclose
 * ---------------------------------------------------------------
 *
 * Modus 'w' legt die Datei neu an (oder leert sie) und schreibt.
 */

$logPfad = $verzeichnis . '/lauf.log';
$log = fopen($logPfad, 'w');
if ($log === false) {
    fwrite(STDERR, "Konnte {$logPfad} nicht öffnen.\n");
    exit(1);
}
try {
    foreach (['Start', 'Verarbeitung', 'Ende'] as $schritt) {
        fwrite($log, "Schritt: {$schritt}\n");
    }
} finally {
    // Schließen gehört ins finally: es passiert in jedem Fall.
    fclose($log);
}
echo 'Log-Zeilen: ' . count(file($logPfad)) . PHP_EOL;

/*
 * ---------------------------------------------------------------
 * 5. Ein Stream, der keine Datei ist: php://temp
 * ---------------------------------------------------------------
 *
 * fopen öffnet nicht nur Dateien, sondern auch Streams. php://temp
 * verhält sich wie eine Datei, lebt aber im Speicher (und weicht erst
 * bei Überlänge auf die Platte aus). Ideal für Zwischenpuffer.
 */

$puffer = fopen('php://temp', 'r+');
fwrite($puffer, 'Zwischenspeicher ohne echte Datei.');
// Zum Lesen den Zeiger an den Anfang zurücksetzen.
rewind($puffer);
echo 'Aus dem Stream: ' . stream_get_contents($puffer) . PHP_EOL;
fclose($puffer);

// Aufräumen: die Beispieldateien wieder entfernen.
unlink($pfad);
unlink($logPfad);
rmdir($verzeichnis);
