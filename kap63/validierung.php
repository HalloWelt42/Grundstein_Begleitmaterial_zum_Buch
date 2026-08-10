<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 63: Reflection und Attribute
 *
 * Der attributbasierte Validierer im Einsatz: einmal mit gültigen, einmal
 * mit fehlerhaften Daten. Der Validierer liest die Regeln allein aus den
 * Attributen an der Klasse Registrierung.
 *
 * Start:  docker run --rm -v "$PWD":/app -w /app php:8.4-cli php validierung.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Registrierung;
use App\Validierer;

$validierer = new Validierer();

// 1) Eine saubere Registrierung - keine Regel wird verletzt.
$gut = new Registrierung(name: 'Ada Lovelace', email: 'ada@example.org', alter: 36);

$verstoesse = $validierer->pruefe($gut);
echo 'Gültige Registrierung: '
    . ($verstoesse === [] ? 'keine Verstöße.' : count($verstoesse) . ' Verstöße.')
    . PHP_EOL;

// 2) Eine fehlerhafte Registrierung - leerer Name, zu lange Adresse,
//    unmögliches Alter. Jede verletzte Regel liefert genau eine Meldung.
$schlecht = new Registrierung(
    name: '   ',
    email: 'viel-zu-lange-adresse-klar-oberhalb-vom-limit@beispiel-firma.example',
    alter: 7,
);

echo PHP_EOL . 'Fehlerhafte Registrierung:' . PHP_EOL;
foreach ($validierer->pruefe($schlecht) as $verstoss) {
    echo '  - ' . $verstoss->alsText() . PHP_EOL;
}
