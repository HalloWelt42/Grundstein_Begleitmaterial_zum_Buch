<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 7: UTF-8 und die mb_*-Funktionen
 *
 * Zeigt, warum strlen bei Umlauten scheinbar "falsch" zählt und wie
 * mb_strlen, mb_substr und mb_strtoupper zeichenweise statt byteweise
 * arbeiten. Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

$wort = 'Grüße';

echo "Wort: {$wort}\n\n";

// --- Länge: Bytes gegen Zeichen -----------------------------------------

// strlen zählt BYTES. In UTF-8 belegen ü und ß je zwei Bytes.
echo 'strlen (Bytes):      ' . strlen($wort) . "\n";

// mb_strlen zählt ZEICHEN - das, was ein Mensch als Buchstaben liest.
echo 'mb_strlen (Zeichen): ' . mb_strlen($wort) . "\n\n";

// --- Ausschnitt: byteweise gegen zeichenweise ---------------------------

// substr schneidet nach Bytes. Drei Bytes enden mitten im "ü" - das
// Ergebnis ist kein gültiges UTF-8 mehr. Wir zeigen es darum als Hex.
$rohSchnitt = substr($wort, 0, 3);

// mb_substr schneidet nach Zeichen: die ersten drei ergeben "Grü".
$mbSchnitt = mb_substr($wort, 0, 3);

echo 'substr(0, 3) als Hex:    ' . bin2hex($rohSchnitt) . "\n";
echo 'mb_substr(0, 3):         ' . $mbSchnitt . "\n";
echo 'mb_substr(0, 3) als Hex: ' . bin2hex($mbSchnitt) . "\n\n";

// --- Groß-Schreibung ----------------------------------------------------

// strtoupper kennt nur ASCII: ü und ß bleiben unangetastet.
echo 'strtoupper:    ' . strtoupper($wort) . "\n";

// mb_strtoupper kennt Unicode: ü wird zu Ü, ß wird zu SS.
echo 'mb_strtoupper: ' . mb_strtoupper($wort) . "\n";
