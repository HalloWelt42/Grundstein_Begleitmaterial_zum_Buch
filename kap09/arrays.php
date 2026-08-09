<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 9: Arrays
 *
 * Zeigt die beiden Gesichter des Arrays: die indizierte Liste und das
 * assoziative Array (die Map). Dazu Erzeugen, Zugriff, Anhängen mit []=,
 * verschachtelte Arrays, Destrukturierung, den Spread-Operator sowie
 * foreach mit Wert und Schlüssel. Alle Ausgaben stammen aus einem echten
 * Lauf mit PHP 8.4.
 */

// --- Indizierte Liste ---------------------------------------------------

// Eine Liste erzeugst du mit eckigen Klammern. Die Schlüssel vergibt PHP
// automatisch als fortlaufende Ganzzahlen ab 0.
$farben = ['rot', 'grün', 'blau'];

echo "Zweite Farbe: {$farben[1]}\n";   // Zugriff über den Index

// Anhängen ohne Index: []= hängt hinten an und zählt den Schlüssel selbst.
$farben[] = 'gelb';                     // bekommt den Index 3

echo 'Anzahl Farben: ' . count($farben) . "\n";
echo "Letzte Farbe: {$farben[3]}\n\n";

// --- Assoziatives Array (Map) -------------------------------------------

// Eine Map ordnet eigenen Schlüsseln Werte zu. Der Pfeil => verbindet
// Schlüssel und Wert.
$preise = [
    'kaffee' => 2.50,
    'kuchen' => 3.90,
    'tee'    => 1.95,
];

echo 'Ein Kaffee kostet ' . $preise['kaffee'] . " EUR\n";

// Neuer Eintrag: ein bislang unbekannter Schlüssel legt ihn an.
$preise['saft'] = 2.20;

echo 'Sorten im Angebot: ' . count($preise) . "\n\n";

// --- Verschachtelte Arrays ----------------------------------------------

// Werte dürfen selbst wieder Arrays sein. So entstehen Tabellen und
// Datensätze. Hier: eine Liste von Personen, jede als kleine Map.
$personen = [
    ['name' => 'Ada',   'stadt' => 'London'],
    ['name' => 'Grace', 'stadt' => 'New York'],
];

// Zugriff über zwei Ebenen: erst der Index, dann der Schlüssel.
echo "Erste Person: {$personen[0]['name']} aus {$personen[0]['stadt']}\n\n";

// --- foreach über eine Liste --------------------------------------------

// foreach läuft über jeden Wert. Die Variable rechts von as bekommt
// nacheinander jeden Eintrag.
echo "Alle Farben:\n";
foreach ($farben as $farbe) {
    echo "  - {$farbe}\n";
}
echo "\n";

// --- foreach mit Schlüssel und Wert -------------------------------------

// Mit dem Muster $key => $value bekommst du beides zugleich. Bei einer Map
// ist das der Normalfall.
echo "Preisliste:\n";
foreach ($preise as $sorte => $preis) {
    echo "  {$sorte}: {$preis} EUR\n";
}
echo "\n";

// --- Destrukturierung ---------------------------------------------------

// Eine Liste lässt sich in einem Schritt auf mehrere Variablen verteilen.
$koordinate = [51.5, -0.12];
[$breite, $laenge] = $koordinate;

echo "Breite: {$breite}, Länge: {$laenge}\n";

// Auch nach Schlüsseln geht das - praktisch bei Datensätzen.
['name' => $wer, 'stadt' => $wo] = $personen[1];

echo "Zerlegt: {$wer} wohnt in {$wo}\n\n";

// --- Der Spread-Operator ------------------------------------------------

// Drei Punkte "packen" ein Array in ein anderes hinein. So verbindest du
// Listen bequem zu einer neuen.
$grundfarben = ['rot', 'grün', 'blau'];
$weitere     = ['gelb', 'lila'];
$palette     = [...$grundfarben, ...$weitere, 'schwarz'];

echo 'Palette: ' . implode(', ', $palette) . "\n";
echo 'Größe der Palette: ' . count($palette) . "\n";
