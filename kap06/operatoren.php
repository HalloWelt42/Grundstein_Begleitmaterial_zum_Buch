<?php

declare(strict_types=1);

/**
 * Operatoren im Überblick: arithmetisch, String-Verkettung, kombinierte
 * Zuweisung und logische Verknüpfung.
 */

// Arithmetik.
$sum = 7 + 3;
$diff = 7 - 3;
$product = 7 * 3;
$quotient = 7 / 2;     // Division ergibt einen float: 3.5
$whole = intdiv(7, 2); // ganzzahlige Division: 3
$rest = 7 % 3;         // Modulo: Rest der ganzzahligen Division: 1
$power = 2 ** 10;      // Potenz: 1024

// Kombinierte Zuweisung: rechnen und zuweisen in einem Schritt.
$account = 100;
$account += 50;        // Kurzform für $account = $account + 50;
$account -= 30;        // jetzt 120

// String-Verkettung - der Punkt fügt Text zusammen.
$first = 'Ada';
$last = 'Lovelace';
$name = $first . ' ' . $last;

// Verkettung hat mit .= ebenfalls eine Kurzform.
$greeting = 'Hallo';
$greeting .= ', ' . $name . '!';

// Logische Verknüpfung.
$loggedIn = true;
$isAdmin = false;
$mayEdit = $loggedIn && $isAdmin;   // UND: beide müssen wahr sein
$mayView = $loggedIn || $isAdmin;   // ODER: eines genügt
$blocked = !$loggedIn;              // NICHT: kehrt den Wert um

// --- Ausgabe ---
echo "7 + 3 = {$sum}, 7 - 3 = {$diff}, 7 * 3 = {$product}\n";
echo "7 / 2 = {$quotient}, intdiv(7, 2) = {$whole}, 7 % 3 = {$rest}\n";
echo "2 hoch 10 = {$power}\n";
echo "Name: {$name}\n";
echo "Kontostand: {$account}\n";
echo "{$greeting}\n";
echo 'darf bearbeiten: ' . ($mayEdit ? 'ja' : 'nein') . "\n";
echo 'darf ansehen: ' . ($mayView ? 'ja' : 'nein') . "\n";
echo 'gesperrt: ' . ($blocked ? 'ja' : 'nein') . "\n";
