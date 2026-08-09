<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 8: Kontrollfluss
 *
 * match als moderner, Wert-liefernder und strikt vergleichender
 * Nachfolger von switch. Zum Vergleich derselbe Fall einmal mit switch.
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

// --- match: ein Ausdruck, der einen Wert liefert ------------------------

$statuscode = 404;

// match vergleicht strikt (wie ===) und liefert einen Wert zurück, den
// wir direkt zuweisen. Mehrere Werte je Zweig sind mit Komma erlaubt.
$text = match ($statuscode) {
    200, 201, 204 => 'Erfolg',
    301, 302      => 'Weiterleitung',
    400, 404      => 'Fehler beim Client',
    500, 503      => 'Fehler beim Server',
    default       => 'Unbekannter Code',
};

echo "Status {$statuscode}: {$text}\n";

// --- match ohne festen Wert: als Kette von Bedingungen ------------------

$temperatur = 27;

// Ein match(true) prüft der Reihe nach, welcher Zweig wahr ist. So
// lassen sich auch Bereiche prüfen, nicht nur einzelne Werte.
$kleidung = match (true) {
    $temperatur >= 30 => 'T-Shirt',
    $temperatur >= 20 => 'leichte Jacke',
    $temperatur >= 10 => 'warme Jacke',
    default           => 'Wintermantel',
};

echo "Bei {$temperatur} Grad: {$kleidung}\n";

// --- Strikter Vergleich: match verwechselt Typen nicht ------------------

$eingabe = '1';

// match vergleicht strikt. Der String '1' trifft daher NICHT den int 1,
// sondern fällt in den default-Zweig.
$ergebnis = match ($eingabe) {
    1       => 'Zahl Eins',
    default => 'kein int 1',
};

echo "Eingabe '1' (String): {$ergebnis}\n";

// --- Dieselbe Aufgabe mit switch (zum Vergleich) ------------------------

$statuscode = 404;
$text = '';

// switch vergleicht lose (wie ==), liefert keinen Wert und braucht in
// jedem Zweig ein break, sonst läuft die Ausführung weiter (fall through).
switch ($statuscode) {
    case 200:
    case 201:
    case 204:
        $text = 'Erfolg';
        break;
    case 400:
    case 404:
        $text = 'Fehler beim Client';
        break;
    default:
        $text = 'Unbekannter Code';
}

echo "switch-Variante, Status {$statuscode}: {$text}\n";
