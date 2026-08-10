<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 65: Die fünf PCRE-Funktionen im Überblick
 *
 * Ein regulärer Ausdruck ist ein Muster, das PHP mit den preg_*-Funktionen
 * auf eine Zeichenkette anwendet. Dieselbe Sprache für das Muster, fünf
 * verschiedene Aufgaben: prüfen, alle Treffer holen, ersetzen, berechnend
 * ersetzen und zerlegen. Diese Datei zeigt je ein klares Beispiel.
 */

$text = 'Bestellung 4711 vom Kunden 23';

// 1) preg_match: Gibt es einen Treffer? Rückgabe: 1 = ja, 0 = nein,
//    false = Fehler im Muster. Der erste Treffer landet in $treffer[0].
if (preg_match('/\d+/', $text, $treffer) === 1) {
    echo 'preg_match:            erster Treffer = ' . $treffer[0] . PHP_EOL;
}

// 2) preg_match_all: ALLE Treffer auf einmal. $alle[0] ist die Liste der
//    vollständigen Treffer.
preg_match_all('/\d+/', $text, $alle);
echo 'preg_match_all:        alle Treffer = ' . implode(', ', $alle[0]) . PHP_EOL;

// 3) preg_replace: jede Ziffer durch einen Stern ersetzen.
$maskiert = preg_replace('/\d/', '*', $text);
echo 'preg_replace:          ' . $maskiert . PHP_EOL;

// 4) preg_replace_callback: die Ersetzung wird berechnet - hier jede Zahl
//    verdoppelt. Der Rückruf bekommt den Treffer als Array und liefert
//    den Ersatz als Zeichenkette.
$verdoppelt = preg_replace_callback(
    '/\d+/',
    static fn (array $m): string => (string) ((int) $m[0] * 2),
    $text,
);
echo 'preg_replace_callback: ' . $verdoppelt . PHP_EOL;

// 5) preg_split: an einem Muster zerlegen - hier an einem Komma mit
//    beliebigem Leerraum davor und danach. So wird das Trennzeichen selbst
//    flexibel, was ein starres explode() nicht kann.
$teile = preg_split('/\s*,\s*/', 'Apfel,  Birne ,Kirsche');
echo 'preg_split:            ' . implode(' | ', $teile) . PHP_EOL;
