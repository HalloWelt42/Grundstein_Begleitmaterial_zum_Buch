<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 65: Gruppen, benannte Treffer und Ergebnis-Arrays
 *
 * Runde Klammern gruppieren einen Teil des Musters. Eine fangende Gruppe
 * merkt sich, was sie getroffen hat; eine benannte Gruppe (?<name>...)
 * legt den Treffer zusätzlich unter einem sprechenden Schlüssel ab; eine
 * nicht fangende Gruppe (?:...) gruppiert nur, ohne etwas zu speichern.
 */

// Ein ISO-Datum in seine drei Teile zerlegen - mit benannten Gruppen.
$datum  = '2026-08-10';
$muster = '/^(?<jahr>\d{4})-(?<monat>\d{2})-(?<tag>\d{2})$/';

if (preg_match($muster, $datum, $t) === 1) {
    // Der Treffer-Array trägt BEIDE Schlüssel: den Namen UND die Nummer.
    echo 'jahr (Name):   ' . $t['jahr'] . PHP_EOL;
    echo 'monat (Name):  ' . $t['monat'] . PHP_EOL;
    echo 'tag (Nummer 3): ' . $t[3] . PHP_EOL;
}

// (?:...) gruppiert das optionale Protokoll, ohne einen Treffer zu belegen.
// So bleibt die benannte Gruppe "host" die einzige gespeicherte.
$adresse = '#^(?:https?://)?(?<host>[a-z0-9.-]+)#i';
preg_match($adresse, 'https://Example.org/pfad', $h);
echo 'host:          ' . $h['host'] . PHP_EOL;

// preg_match_all mit PREG_SET_ORDER: pro Zeile ein vollständiger Treffer,
// jeder mit seinen benannten Teilen. Ideal für zeilenweise Protokolle.
$log = "10:00 GET /start\n10:01 POST /login\n10:02 GET /konto";
$zeile = '/(?<zeit>\d{2}:\d{2}) (?<methode>[A-Z]+) (?<pfad>\S+)/';

preg_match_all($zeile, $log, $treffer, PREG_SET_ORDER);
foreach ($treffer as $z) {
    echo $z['zeit'] . ' -> ' . str_pad($z['methode'], 4) . ' ' . $z['pfad'] . PHP_EOL;
}
