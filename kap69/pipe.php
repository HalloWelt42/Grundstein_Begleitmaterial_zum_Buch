<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 69: PHP 8.5 im Detail
 *
 * Der Pipe-Operator |> leitet einen Wert durch eine Kette von Aufrufen.
 * Der linke Wert wird zum einzigen Argument des rechten Aufrufs; dessen
 * Ergebnis fließt weiter in den nächsten Schritt. So liest sich eine
 * Verarbeitung von oben nach unten statt von innen nach außen.
 * Nur mit php:8.5-cli ausführbar.
 */

$roh = '  Grundstein, modernes PHP  ';

// Bisher: von innen nach außen zu lesen - der erste Schritt steht ganz
// innen, der letzte ganz außen.
$verschachtelt = str_replace(
    ',',
    ' -',
    strtoupper(trim($roh)),
);

// Mit dem Pipe-Operator: von oben nach unten, in der Reihenfolge der
// Verarbeitung. Jeder Schritt ist ein Aufruf, der genau ein Argument
// erwartet - den Wert des vorigen Schritts.
$verkettet = $roh
    |> trim(...)
    |> strtoupper(...)
    |> (fn (string $s): string => str_replace(',', ' -', $s));

echo 'Verschachtelt: ' . $verschachtelt . PHP_EOL;
echo 'Verkettet:     ' . $verkettet . PHP_EOL;
echo 'Gleiches Ergebnis: ' . ($verschachtelt === $verkettet ? 'ja' : 'nein') . PHP_EOL;

echo PHP_EOL;

// Auch eigene Funktionen lassen sich als First-Class-Callable einhängen.
function nurBuchstaben(string $text): string
{
    return preg_replace('/[^A-Za-z ]/', '', $text);
}

function woerter(string $text): array
{
    return explode(' ', trim($text));
}

// Zählt die Wörter, nachdem Ziffern und Sonderzeichen entfernt wurden.
$anzahl = '  PHP 8.5 bringt den Pipe-Operator  '
    |> nurBuchstaben(...)
    |> woerter(...)
    |> array_filter(...)   // leere Teile entfernen
    |> count(...);

echo 'Wörter: ' . $anzahl . PHP_EOL;
