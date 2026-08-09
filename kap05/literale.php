<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 5: Werte schreiben (Literale).
 *
 * Denselben Zahlenwert kann man auf mehreren Wegen hinschreiben, und
 * Zeichenketten in einfachen oder doppelten Anführungszeichen verhalten
 * sich unterschiedlich. Dieses Skript zeigt die wichtigsten Formen.
 */

// --- Ganzzahlen in verschiedenen Zahlensystemen: alle sind dieselbe 255 ---
$dezimal     = 255;      // gewohnt dezimal
$hexadezimal = 0xFF;     // 0x = Sechzehnersystem
$oktal       = 0o377;    // 0o = Achtersystem (moderne Schreibweise)
$binaer      = 0b11111111; // 0b = Zweiersystem

echo $dezimal . ' ' . $hexadezimal . ' ' . $oktal . ' ' . $binaer . PHP_EOL;

// Unterstriche gliedern große Zahlen, ohne den Wert zu verändern.
$einwohner = 83_200_000;
echo $einwohner . PHP_EOL;

// --- Gleitkommazahlen: Punkt als Dezimaltrennung, e für Zehnerpotenzen ---
$pi         = 3.14;
$lichtjahr  = 9.46e15;   // 9,46 mal 10 hoch 15
echo $pi . ' ' . $lichtjahr . PHP_EOL;

// --- Zeichenketten: einfache vs. doppelte Anführungszeichen ---
$name = 'PHP';

// Einfache Anführungszeichen geben den Inhalt fast wörtlich aus.
echo 'Sprache: $name' . PHP_EOL;

// Doppelte Anführungszeichen setzen Variablen ein und deuten \n als Umbruch.
echo "Sprache: $name (mit Interpolation)\n";
