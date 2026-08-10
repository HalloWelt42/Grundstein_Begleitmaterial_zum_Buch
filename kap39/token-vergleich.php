<?php

declare(strict_types=1);

/**
 * Zeigt losgelöst vom Web, dass der Token-Vergleich mit hash_equals()
 * genau das Erwartete tut: Ein identisches Token gilt, ein abweichendes
 * und ein leeres werden abgelehnt. Dieselbe Vergleichslogik steckt in
 * csrfPruefen() aus csrf.php - hier ohne Session herausgezogen, damit
 * sie sich direkt im CLI vorführen lässt.
 *
 * Läuft ohne Server direkt im CLI:
 *   docker run --rm -v "$PWD":/app -w /app php:8.4-cli php kap39/token-vergleich.php
 */

/**
 * Prüft ein erhaltenes Token gegen das erwartete. hash_equals()
 * vergleicht in konstanter Zeit und verrät über die Antwortdauer
 * nichts über die Anzahl übereinstimmender Zeichen. Ein fehlendes
 * oder leeres Token gilt nie als gültig.
 */
function tokenGueltig(string $erwartet, ?string $erhalten): bool
{
    return $erhalten !== null
        && $erwartet !== ''
        && hash_equals($erwartet, $erhalten);
}

// Das in der Session hinterlegte Token - hier fest erzeugt statt aus
// $_SESSION gelesen, damit das Beispiel ohne Webserver auskommt.
$erwartet = bin2hex(random_bytes(32));

// Drei Fälle: dasselbe Token, ein anderes und ein leeres.
$faelle = [
    'gleiches Token' => $erwartet,
    'falsches Token' => bin2hex(random_bytes(32)),
    'leeres Token'   => '',
];

foreach ($faelle as $name => $erhalten) {
    $ergebnis = tokenGueltig($erwartet, $erhalten) ? 'gültig' : 'abgelehnt';
    echo str_pad($name . ':', 17) . $ergebnis . PHP_EOL;
}
