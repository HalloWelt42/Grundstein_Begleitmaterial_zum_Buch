<?php

declare(strict_types=1);

/*
 * Steht für eine einzelne "Anfrage": lädt die Bibliothek und tut danach
 * fast nichts. So zeigt die gemessene Zeit im Kern die Kompilierkosten -
 * genau den Schritt, den der OPcache einspart. Die Datei misst ihre eigene
 * Ladezeit und gibt sie in Millisekunden aus; das Vergleichsskript
 * (opcache-vergleich.php) ruft sie viele Male auf und mittelt die Werte.
 */

$start = hrtime(true);
require __DIR__ . '/lib.php';
$dauer = (hrtime(true) - $start) / 1_000_000; // Nanosekunden -> Millisekunden

// Nur die reine Zahl ausgeben, damit das aufrufende Skript sie leicht liest.
echo $dauer, PHP_EOL;
