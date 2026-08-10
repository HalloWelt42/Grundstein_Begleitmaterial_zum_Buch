<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Vorher)
 *
 * Ein winziger Treiber, der drei "Formular-Absendungen" an das
 * vermischte Skript nachstellt - nur damit wir es überhaupt laufen
 * sehen. Genau diese Verrenkung (Superglobals von Hand setzen, die
 * Ausgabe abfangen, eine Datei-Datenbank vorher löschen) ist der beste
 * Beweis, wie schwer sich so ein Skript prüfen lässt.
 */

// Vor dem Lauf die Datei-Datenbank leeren, damit der Test wiederholbar ist.
$db = __DIR__ . '/anmeldung.sqlite';
if (is_file($db)) {
    unlink($db);
}

/**
 * Stellt eine einzelne Anfrage nach: setzt $_POST, bindet das Skript ein
 * und gibt dessen abgefangene HTML-Ausgabe als Zeichenkette zurück.
 */
function anfrage(string $email): string
{
    $_POST = ['email' => $email];

    ob_start();
    require __DIR__ . '/anmeldung.php';

    return trim((string) ob_get_clean());
}

// Erst alle Antworten sammeln, dann ausgeben - so bleibt die Ausgabe klar.
$antworten = [
    anfrage('ada@example.org'),   // neu - wird angelegt
    anfrage('ada@example.org'),   // schon vorhanden - abgelehnt
    anfrage('kein-at-zeichen'),   // ungültig - abgelehnt
];

echo implode(PHP_EOL, $antworten) . PHP_EOL;

// Aufräumen: die Datei-Datenbank gehört nicht ins Begleitmaterial.
if (is_file($db)) {
    unlink($db);
}
