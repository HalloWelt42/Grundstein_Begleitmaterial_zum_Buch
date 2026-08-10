<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 62: Array gegen Generator im Speichervergleich
 *
 * Dieselbe Aufgabe - die Zahlen 1 bis N aufsummieren - auf zwei Wegen.
 * Der eine hält alle Zahlen gleichzeitig in einem Array, der andere
 * erzeugt sie eine nach der anderen mit einem Generator. Gemessen wird
 * der Spitzenspeicher (memory_get_peak_usage), also der höchste Stand,
 * den der Prozess bis hierher erreicht hat.
 *
 * Aufruf:
 *   php array-vs-generator.php array       (alle Zahlen im Array)
 *   php array-vs-generator.php generator   (Zahl für Zahl)
 *   php array-vs-generator.php generator 5000000   (eigene Anzahl)
 */

$modus  = $argv[1] ?? 'generator';
$anzahl = (int) ($argv[2] ?? 1_000_000);

/**
 * Erzeugt die Zahlen 1..$anzahl Stück für Stück. yield gibt jeweils genau
 * eine Zahl heraus und hält den Stand der Schleife bis zum nächsten Zug
 * fest - alle anderen Zahlen existieren zu diesem Zeitpunkt noch gar nicht.
 */
function zahlenstrom(int $anzahl): Generator
{
    for ($i = 1; $i <= $anzahl; $i++) {
        yield $i;
    }
}

$summe = 0;

if ($modus === 'array') {
    // Erst alle Zahlen vollständig in ein Array - das kostet Speicher
    // proportional zur Anzahl.
    $zahlen = range(1, $anzahl);
    foreach ($zahlen as $zahl) {
        $summe += $zahl;
    }
} else {
    // Der Generator hält immer nur eine Zahl - der Speicher bleibt flach,
    // egal wie groß die Anzahl wird.
    foreach (zahlenstrom($anzahl) as $zahl) {
        $summe += $zahl;
    }
}

$spitze = memory_get_peak_usage();

printf('Modus:           %s' . PHP_EOL, $modus);
printf('Elemente:        %s' . PHP_EOL, number_format($anzahl, 0, ',', '.'));
printf('Summe:           %s' . PHP_EOL, number_format($summe, 0, ',', '.'));
printf('Spitzenspeicher: %.2f MB' . PHP_EOL, $spitze / 1024 / 1024);
