<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 69: PHP 8.5 im Detail
 *
 * array_first() und array_last() liefern das erste bzw. letzte Element
 * eines Arrays - klar benannt und ohne den Umweg über reset()/end() (die
 * einen internen Zeiger verschieben) oder über array_key_first().
 * Nur mit php:8.5-cli ausführbar.
 */

$namen = ['Ada', 'Grace', 'Linus', 'Margaret'];

echo 'Erstes:  ' . array_first($namen) . PHP_EOL;
echo 'Letztes: ' . array_last($namen) . PHP_EOL;

echo PHP_EOL;

// Auch bei einer Map mit String-Schlüsseln, ohne den Schlüssel zu kennen.
$preise = ['klein' => 990, 'mittel' => 1490, 'gross' => 1990];

echo 'Erster Eintrag:  ' . array_first($preise) . PHP_EOL;
echo 'Letzter Eintrag: ' . array_last($preise) . PHP_EOL;

echo PHP_EOL;

// Ein leeres Array liefert null - kein Fehler, kein verschobener Zeiger.
var_dump(array_first([]));
var_dump(array_last([]));
