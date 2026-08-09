<?php

declare(strict_types=1);

/**
 * Konstanten: unveränderliche Werte benennen.
 *
 * const gilt zur Übersetzungszeit und steht ganz oben im Skript oder in
 * einer Klasse. define() setzt eine Konstante zur Laufzeit. Klassen-
 * konstanten werden hier nur kurz angerissen - die Vertiefung folgt in
 * Teil III.
 */

// Konstante auf oberster Ebene mit const.
const MEHRWERTSTEUER = 0.19;

// define() setzt eine Konstante zur Laufzeit.
define('APP_NAME', 'Grundstein');

// Typisierte Klassenkonstante (seit PHP 8.3): fester Typ, fester Wert.
final class Kreis
{
    public const float PI = 3.14159;
}

$netto = 100.0;

// Konstanten lassen sich wie Werte in Ausdrücken verwenden.
$brutto = $netto * (1 + MEHRWERTSTEUER);

echo APP_NAME . ": Netto {$netto} Euro ergibt Brutto "
    . number_format($brutto, 2, ',', '.') . " Euro\n";

echo 'Kreiszahl (gerundet): ' . Kreis::PI . "\n";

// Konstanten sind unveränderlich: eine erneute Zuweisung an
// MEHRWERTSTEUER wäre ein Fehler und wird vom Interpreter abgelehnt.
echo 'Steuersatz bleibt: ' . (MEHRWERTSTEUER * 100) . " Prozent\n";
