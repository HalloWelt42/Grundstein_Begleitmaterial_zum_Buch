<?php

declare(strict_types=1);

/**
 * Variablen: Werte benennen, kopieren und referenzieren.
 *
 * Zeigt die Wertsemantik (die Zuweisung erzeugt eine Kopie) und die
 * Referenzsemantik (mit & zeigen zwei Namen auf denselben Wert).
 */

// Zuweisung: ein Name für einen Wert.
$name = 'Ada';
$age = 36;
$active = true;
echo "Start: {$name}, {$age} Jahre, aktiv=" . ($active ? 'ja' : 'nein') . "\n";

// Wertsemantik: die Zuweisung kopiert den Wert.
$original = 10;
$copy = $original;   // eigenständige Kopie
$copy = 20;
echo "Wertsemantik: original={$original}, copy={$copy}\n";

// Referenzsemantik: & bindet zwei Namen an denselben Wert.
$score = 10;
$ref = &$score;      // $ref zeigt auf denselben Platz wie $score
$ref = 99;
echo "Referenz: score={$score}, ref={$ref}\n";

// Auch Arrays werden bei der Zuweisung kopiert, nicht geteilt.
$listA = [1, 2, 3];
$listB = $listA;
$listB[] = 4;
echo 'Array-Kopie: A hat ' . count($listA) . ' Elemente, B hat ' . count($listB) . "\n";

// Referenzen wieder trennen: unset löst nur die Bindung des Namens.
unset($ref);
$score = 0;
echo "Nach unset: score={$score}\n";
