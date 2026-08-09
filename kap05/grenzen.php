<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 5: Die Grenzen von int und float.
 *
 * Ganzzahlen haben eine feste Obergrenze. Wird sie überschritten,
 * weicht PHP still auf float aus. Und Gleitkommazahlen sind im
 * Binärsystem nicht immer exakt - deshalb vergleicht man sie nie mit
 * === auf Gleichheit, sondern über eine Toleranz.
 */

// Die größte ganze Zahl, die dieser Rechner exakt als int hält.
echo 'PHP_INT_MAX = ' . PHP_INT_MAX . PHP_EOL;
echo 'Typ:          ' . get_debug_type(PHP_INT_MAX) . PHP_EOL;

// Eins darüber passt nicht mehr in einen int - PHP macht daraus float.
$ueberlauf = PHP_INT_MAX + 1;
echo 'PHP_INT_MAX + 1 = ' . $ueberlauf
    . ' (' . get_debug_type($ueberlauf) . ')' . PHP_EOL;

echo str_repeat('-', 40) . PHP_EOL;

// Der Klassiker: 0.1 + 0.2 ist im Binärsystem nicht exakt darstellbar.
$summe = 0.1 + 0.2;
echo '0.1 + 0.2        = ' . $summe . PHP_EOL;            // sieht nach 0.3 aus
var_dump($summe === 0.3);                                  // ... ist es aber nicht

// Bei Werten dieser Größenordnung (um 1) genügt PHP_FLOAT_EPSILON.
$istGleich = abs($summe - 0.3) < PHP_FLOAT_EPSILON;
var_dump($istGleich);

echo str_repeat('-', 40) . PHP_EOL;

// Bei größeren Werten reicht die feste Schranke NICHT: Der Abstand zweier
// benachbarter float wächst mit der Größenordnung, PHP_FLOAT_EPSILON
// (der Abstand bei 1.0) ist dann viel zu streng.
$a = 1000.1 + 0.2;   // Größenordnung 1000
$b = 1000.3;
var_dump(abs($a - $b) < PHP_FLOAT_EPSILON);               // false - zu streng!

// Robust: die Toleranz mit den Werten selbst skalieren.
$gleich = abs($a - $b) <= PHP_FLOAT_EPSILON * max(abs($a), abs($b));
var_dump($gleich);                                        // true
