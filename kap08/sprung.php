<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 8: Kontrollfluss
 *
 * break und continue steuern den Ablauf innerhalb von Schleifen; mit einer
 * Ebenen-Angabe wirken sie auf mehrere verschachtelte Schleifen. Alle
 * Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

// --- continue: den Rest eines Durchlaufs überspringen -------------------

// continue springt zum nächsten Durchlauf. Hier überspringen wir die
// geraden Zahlen und geben nur die ungeraden aus.
echo 'ungerade: ';
for ($i = 1; $i <= 10; $i++) {
    if ($i % 2 === 0) {
        continue;
    }
    echo $i . ' ';
}
echo "\n";

// --- break: die Schleife ganz verlassen ---------------------------------

// break beendet die Schleife sofort. Wir suchen die erste durch 7 teilbare
// Zahl und hören auf, sobald wir sie gefunden haben.
echo 'erste Vielfache von 7: ';
for ($i = 1; $i <= 100; $i++) {
    if ($i % 7 === 0) {
        echo $i . "\n";
        break;
    }
}

// --- break mit Ebenen-Angabe --------------------------------------------

// In verschachtelten Schleifen bricht "break 2" beide Schleifen auf einmal
// ab. Wir suchen ein Zahlenpaar, dessen Produkt genau 12 ergibt.
echo 'Paar mit Produkt 12: ';
for ($a = 1; $a <= 5; $a++) {
    for ($b = 1; $b <= 5; $b++) {
        if ($a * $b === 12) {
            echo "{$a} mal {$b}\n";
            break 2;   // verlässt beide for-Schleifen
        }
    }
}

// --- continue mit Ebenen-Angabe -----------------------------------------

// "continue 2" springt sofort zum nächsten Durchlauf der äußeren Schleife.
// Wir geben nur Paare aus, bei denen die Spalte nicht größer als die
// Zeile ist; sobald sie es wird, überspringen wir den Rest der Zeile.
echo "Dreieck der Paare:\n";
for ($zeile = 1; $zeile <= 3; $zeile++) {
    for ($spalte = 1; $spalte <= 5; $spalte++) {
        if ($spalte > $zeile) {
            continue 2;   // Rest der Zeile überspringen, nächste Zeile
        }
        echo "  Zeile {$zeile}, Spalte {$spalte}\n";
    }
}
