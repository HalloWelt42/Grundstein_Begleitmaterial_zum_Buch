<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben (Vorher)
 *
 * Zeigt, dass die Klasse zwar läuft, aber nicht vorhersagbar ist: Zwei
 * Gutscheine, kurz nacheinander erzeugt, tragen verschiedene Codes. Ein
 * Test hätte keinen bekannten Wert, gegen den er prüfen könnte - genau
 * das ist der Kern des Problems.
 */

require __DIR__ . '/Gutschein.php';

$a = new \App\Vorher\Gutschein(1000);
$b = new \App\Vorher\Gutschein(1000);

echo 'Code A:      ' . $a->code . PHP_EOL;
echo 'Code B:      ' . $b->code . PHP_EOL;
echo 'Verschieden: ' . ($a->code !== $b->code ? 'ja' : 'nein') . PHP_EOL;
echo 'Gültig:      ' . ($a->istGueltig() ? 'ja' : 'nein') . PHP_EOL;
