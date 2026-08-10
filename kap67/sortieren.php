<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 67: Internationalisierung
 *
 * Collator sortiert sprachrichtig. Ein roher Byte-Vergleich (sort) ordnet
 * Umlaute falsch ein, weil er die UTF-8-Bytes vergleicht statt der
 * Buchstaben - Umlaute landen dann hinter dem Z. Der Collator kennt die
 * Regeln des jeweiligen Sprachraums und stellt Ä neben A, Ö neben O.
 */

$woerter = ['Zeder', 'Apfel', 'Öl', 'Bär', 'Uhr', 'Ähre', 'apfel'];

echo '--- Roher Byte-Vergleich (sort) ---' . PHP_EOL;
$bytes = $woerter;
sort($bytes); // vergleicht Bytes - Umlaute rutschen ans Ende
echo implode(', ', $bytes) . PHP_EOL;

echo PHP_EOL . '--- Collator de-DE ---' . PHP_EOL;
$collator = new Collator('de-DE');
$deutsch = $woerter;
$collator->sort($deutsch);
echo implode(', ', $deutsch) . PHP_EOL;

echo PHP_EOL . '--- Collator sv-SE (Schwedisch ordnet anders) ---' . PHP_EOL;
// Im Schwedischen ist Ö ein eigener Buchstabe ganz am Ende des Alphabets -
// dieselbe Liste, eine andere richtige Reihenfolge.
$schwedisch = $woerter;
(new Collator('sv-SE'))->sort($schwedisch);
echo implode(', ', $schwedisch) . PHP_EOL;
