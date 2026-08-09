<?php

declare(strict_types=1);

/**
 * Vergleiche und moderne Kurzformen.
 *
 * Zeigt den Unterschied zwischen == (loser Vergleich mit Typumwandlung)
 * und === (strikter Vergleich), dazu Null-Koaleszenz, ternären und
 * Elvis-Operator sowie den Spaceship-Operator zum Sortieren.
 */

// == prüft nur den Wert (mit Typumwandlung), === auch den Typ.
var_dump('1' == 1);      // true:  Text "1" wird zur Zahl 1
var_dump('1' === 1);     // false: string ist nicht int
var_dump('10' == '1e1'); // true:  zwei numerische Strings, beide 10
var_dump(null == false); // true:  beide gelten als "leer"

echo "---\n";

// Null-Koaleszenz: nimm den Wert, sonst den Ersatz.
$config = ['name' => 'Ada'];
$name = $config['name'] ?? 'Unbekannt';
$mail = $config['mail'] ?? 'keine Adresse';   // Schlüssel fehlt -> Ersatz
echo "Name: {$name}, Mail: {$mail}\n";

// ??= weist nur zu, wenn noch kein Wert da ist.
$settings = [];
$settings['theme'] ??= 'hell';    // wird gesetzt
$settings['theme'] ??= 'dunkel';  // bleibt "hell", weil schon belegt
echo "Theme: {$settings['theme']}\n";

// Ternär: kurze Wenn-dann-sonst-Auswahl.
$points = 75;
$status = $points >= 50 ? 'bestanden' : 'durchgefallen';
echo "Ergebnis: {$status}\n";

// Elvis: nimm links, falls "truthy", sonst rechts.
$input = '';
$display = $input ?: 'Standardwert';
echo "Anzeige: {$display}\n";

echo "---\n";

// Spaceship <=> : liefert -1, 0 oder 1 - ideal zum Sortieren.
var_dump(1 <=> 2);   // -1: links kleiner
var_dump(2 <=> 2);   //  0: gleich
var_dump(3 <=> 2);   //  1: links größer

// usort nutzt den Spaceship-Operator direkt als Vergleichsregel.
$numbers = [3, 1, 4, 1, 5, 9, 2, 6];
usort($numbers, fn(int $a, int $b): int => $a <=> $b);
echo 'Sortiert: ' . implode(', ', $numbers) . "\n";
