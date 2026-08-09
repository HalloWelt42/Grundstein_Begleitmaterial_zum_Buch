<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 7: Formatierte Ausgabe mit sprintf und printf
 *
 * printf gibt eine formatierte Zeichenkette direkt aus; sprintf liefert
 * sie als Rückgabewert zurück. Alle Ausgaben stammen aus einem echten
 * Lauf mit PHP 8.4.
 */

// --- Die wichtigsten Platzhalter ----------------------------------------

// %s Zeichenkette, %d Ganzzahl, %f Fließkommazahl.
printf("%s hat %d Beine.\n", 'Die Spinne', 8);

// %.2f zeigt genau zwei Nachkommastellen und rundet darauf.
printf("Preis: %.2f EUR\n", 2.5);

// Feldbreite und Ausrichtung: %8.2f ist rechtsbündig auf acht Zeichen,
// %-8s linksbündig auf acht Zeichen.
printf("[%8.2f]\n", 3.14159);
printf("[%-8s]\n", 'links');

// Führende Nullen mit %05d - praktisch für Nummern fester Breite.
printf("Nr. %05d\n", 42);

// Vorzeichen erzwingen mit %+d.
printf("%+d und %+d\n", 5, -5);

// Andere Zahlensysteme: %x hexadezimal, %b binär.
printf("255 = %x (hex) = %b (binär)\n", 255, 255);

// Ein echtes Prozentzeichen schreibt man als %%.
printf("Rabatt: %d%%\n", 20);

// --- Eine kleine, sauber ausgerichtete Rechnung -------------------------

echo "\n";

// Jeder Posten: Name, Anzahl, Einzelpreis.
$posten = [
    ['Kaffee', 3, 2.5],
    ['Kuchen', 2, 3.9],
    ['Tee', 1, 1.95],
];

$summe = 0.0;

// Destrukturierung direkt im Kopf der Schleife: [$name, $anzahl, $einzel].
foreach ($posten as [$name, $anzahl, $einzel]) {
    $zwischen = $anzahl * $einzel;
    $summe += $zwischen;

    // Feste Spaltenbreiten sorgen für saubere Ausrichtung.
    printf("%-8s %2d x %6.2f = %7.2f EUR\n", $name, $anzahl, $einzel, $zwischen);
}

// sprintf baut die Summenzeile als Zeichenkette; echo gibt sie aus.
// Die Breite 21 lässt die Summe an derselben Spalte enden wie die Posten.
$summenzeile = sprintf("%-8s %21.2f EUR", 'Summe', $summe);
echo $summenzeile . "\n";

// --- Deutsche Geldanzeige mit number_format -----------------------------

echo "\n";

// %.2f nutzt immer den Punkt als Dezimaltrenner. Für die nutzerseitige
// deutsche Schreibweise sorgt number_format: Komma als Dezimaltrenner,
// Punkt als Tausendertrenner.
$betrag = 1234.5;
echo 'Mit printf:        ' . sprintf("%.2f", $betrag) . " EUR\n";
echo 'Mit number_format: ' . number_format($betrag, 2, ',', '.') . " EUR\n";
