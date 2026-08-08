<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 2: Dein erstes Programm.
 * Ein Skript ist eine Textdatei, die PHP Zeile für Zeile von oben nach
 * unten abarbeitet. Wir speichern zwei Werte und geben einen Satz aus.
 */

$sprache = 'PHP';
$version = PHP_VERSION;   // eingebaute Konstante: die gerade laufende Version.

echo "Hallo, {$sprache} {$version}!" . PHP_EOL;
