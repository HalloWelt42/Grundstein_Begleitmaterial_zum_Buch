<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang B: Mathematik-Funktionen
 *
 * Ganzzahlige und gefahrlose Division, Runden mit Modus, sichere
 * Zufallszahlen sowie das Anzeigen von Zahlen. Ausgaben aus echtem
 * 8.4-Lauf.
 */

// --- Teilen ------------------------------------------------------------

echo intdiv(17, 5), "\n";        // ganzzahliger Quotient
echo 17 % 5, "\n";               // Rest der Ganzzahldivision
echo fdiv(1, 0), "\n";           // gefahrlos: INF statt Fehler

// --- Runden mit Modus --------------------------------------------------

echo round(2.5), "\n";                            // 3 (kaufmännisch auf)
echo round(2.5, 0, PHP_ROUND_HALF_DOWN), "\n";    // 2 (halbe abrunden)
echo round(3.14159, 2), "\n";                     // zwei Nachkommastellen
echo ceil(4.1), ' ', floor(4.9), "\n";            // auf / ab zur Ganzzahl

// --- Sichere Zufallszahlen ---------------------------------------------

$augen = random_int(1, 6);       // kryptographisch sicher
echo ($augen >= 1 && $augen <= 6) ? "gültig\n" : "ungültig\n";

// --- Anzeigen ----------------------------------------------------------

echo number_format(1234567.891, 2, ',', '.'), "\n";
echo max(3, 7, 2), ' ', min([3, 7, 2]), ' ', abs(-8), "\n";
