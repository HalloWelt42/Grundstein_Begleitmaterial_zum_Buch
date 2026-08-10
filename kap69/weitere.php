<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 69: PHP 8.5 im Detail
 *
 * Ein knapper Blick auf weitere Neuerungen von PHP 8.5.
 * Nur mit php:8.5-cli ausführbar.
 */

// 1) Den aktuell gesetzten Fehler- und Ausnahme-Handler abfragen, ohne
//    ihn zu ersetzen. Bisher ließ sich der aktive Handler nur ermitteln,
//    indem man ihn mit set_error_handler() überschrieb und den alten
//    Rückgabewert auffing - umständlich und mit Nebenwirkung.
set_error_handler(static fn (int $stufe, string $text): bool => true);

$fehlerHandler = get_error_handler();
echo 'Fehler-Handler gesetzt:   ' . ($fehlerHandler instanceof Closure ? 'ja' : 'nein') . PHP_EOL;
echo 'Ausnahme-Handler gesetzt: ' . (get_exception_handler() === null ? 'nein' : 'ja') . PHP_EOL;

restore_error_handler();

// 2) Die Konstante PHP_BUILD_DATE nennt den Zeitpunkt, zu dem dieser
//    PHP-Build erzeugt wurde.
echo 'Build-Datum vorhanden:    ' . (defined('PHP_BUILD_DATE') ? 'ja' : 'nein') . PHP_EOL;

// 3) Der (void)-Cast (aus dem Umfeld von #[\NoDiscard]) verwirft einen
//    Wert ausdrücklich - er dokumentiert die Absicht direkt im Code.
(void) strlen('nur der Rückgabewert zählt, hier wird er bewusst verworfen');
echo 'void-Cast akzeptiert:     ja' . PHP_EOL;
