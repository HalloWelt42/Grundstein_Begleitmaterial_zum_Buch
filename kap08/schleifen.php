<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 8: Kontrollfluss
 *
 * Die vier Schleifenformen for, while, do-while und foreach. foreach greift
 * Arrays vor (ausführlich in Kapitel 9). Alle Ausgaben stammen aus einem
 * echten Lauf mit PHP 8.4.
 */

// --- for: Zählschleife mit bekannter Anzahl -----------------------------

// Kopf in drei Teilen: Start; Bedingung; Schritt. Solange die Bedingung
// wahr ist, läuft der Rumpf, danach folgt der Schritt.
echo 'for:      ';
for ($i = 1; $i <= 5; $i++) {
    echo $i . ' ';
}
echo "\n";

// --- while: Schleife mit Bedingung am Anfang ----------------------------

// Die Bedingung wird vor jedem Durchlauf geprüft. Ist sie von Beginn an
// falsch, läuft der Rumpf gar nicht.
echo 'while:    ';
$rest = 16;
while ($rest > 1) {
    echo $rest . ' ';
    $rest = intdiv($rest, 2);   // ganzzahlig halbieren
}
echo "\n";

// --- do-while: Schleife mit Bedingung am Ende ---------------------------

// Der Rumpf läuft mindestens einmal, weil die Bedingung erst danach
// geprüft wird - ideal für "erst tun, dann prüfen".
echo 'do-while: ';
$zahl = 42;
do {
    echo $zahl . ' ';
    $zahl -= 10;
} while ($zahl > 0);
echo "\n";

// --- foreach: durch eine Sammlung laufen (Vorgriff Kapitel 9) -----------

$preise = ['Kaffee' => 2.5, 'Kuchen' => 3.9, 'Tee' => 1.95];

// foreach ist die natürliche Wahl für Arrays: kein Zähler, kein
// Verzählen. Mit "Schlüssel => Wert" bekommst du beides.
echo "foreach:\n";
$summe = 0.0;
foreach ($preise as $name => $preis) {
    $summe += $preis;
    printf("  %-8s %5.2f EUR\n", $name, $preis);
}
printf("  %-8s %5.2f EUR\n", 'Summe', $summe);
