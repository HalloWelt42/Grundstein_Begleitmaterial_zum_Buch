<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 11: Ein nicht abgefangener Fehler hält an.
 *
 * Anders als eine Warnung bricht ein Error die Ausführung ab, sobald ihn
 * niemand mit try/catch abfängt. Die Zeile hinter dem Aufruf wird dann nie
 * erreicht. Das ist gewollt: Bei einem echten Typfehler weiterzurechnen
 * wäre gefährlicher als anzuhalten.
 */

function quadrat(int $zahl): int
{
    return $zahl * $zahl;
}

echo 'Vor dem Aufruf.' . PHP_EOL;

// Nicht abgefangen: Dieser Aufruf beendet das Skript mit einem TypeError.
echo quadrat('vier') . PHP_EOL;

echo 'Diese Zeile wird nie erreicht.' . PHP_EOL;
