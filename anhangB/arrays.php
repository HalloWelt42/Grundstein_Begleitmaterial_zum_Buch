<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang B: Array-Funktionen
 *
 * map/filter/reduce, Spread und Destrukturierung, das Umformen von
 * Tabellen mit array_column/array_combine sowie zwei Neuerungen aus
 * PHP 8.4 (array_is_list, array_find). Ausgaben aus echtem 8.4-Lauf.
 */

// --- map / filter / reduce ---------------------------------------------

$zahlen = [1, 2, 3, 4, 5, 6];

$quadrate = array_map(fn (int $n): int => $n * $n, $zahlen);
$gerade   = array_filter($zahlen, fn (int $n): bool => $n % 2 === 0);
$summe    = array_reduce($zahlen, fn (int $t, int $n): int => $t + $n, 0);

echo implode(', ', $quadrate), "\n";
echo implode(', ', array_values($gerade)), "\n";
echo "Summe: {$summe}\n";

// --- Spread und Destrukturierung ---------------------------------------

$a = [1, 2];
$b = [3, 4];
$alle = [...$a, ...$b, 5];              // Listen zusammenfügen

[$erst, , $dritt] = $alle;             // zweites Element überspringen

echo implode(', ', $alle), "\n";
echo "erst={$erst}, dritt={$dritt}\n";

// --- Tabellen umformen -------------------------------------------------

$personen = [
    ['id' => 7, 'name' => 'Ada'],
    ['id' => 9, 'name' => 'Grace'],
];

$namen = array_column($personen, 'name');         // eine Spalte
$index = array_column($personen, 'name', 'id');   // Map id => name

echo implode(', ', $namen), "\n";
echo $index[9], "\n";

// --- Neu in PHP 8.4 ----------------------------------------------------

var_dump(array_is_list([0 => 'a', 1 => 'b']));    // lückenlos ab 0?
$treffer = array_find($personen, fn (array $p): bool => $p['id'] > 8);
echo $treffer['name'], "\n";
