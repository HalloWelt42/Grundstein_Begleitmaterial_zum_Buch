<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 65: Katastrophales Backtracking
 *
 * Ein schlecht gebautes Muster kann den Motor in exponentiell viele
 * Versuche treiben. Das Muster /^(a+)+$/ verlangt, die vielen a einer
 * Eingabe auf zwei verschachtelte Quantoren aufzuteilen - dafür gibt es
 * astronomisch viele Möglichkeiten. Kommt am Ende ein Zeichen, das nicht
 * passt, probiert PCRE sie alle durch. PHP bricht bei Erreichen des
 * pcre.backtrack_limit ab und liefert false (NICHT 0) samt Fehlercode.
 */

// Alle a matchen den Kern, das "!" am Ende lässt das Muster nie aufgehen -
// die perfekte Falle für verschachtelte Quantoren.
$eingabe = str_repeat('a', 10000) . '!';

$boese = '/^(a+)+$/';

$start     = hrtime(true);
$ergebnis  = preg_match($boese, $eingabe);
$dauerMs   = (hrtime(true) - $start) / 1_000_000;

echo '--- böses Muster /^(a+)+$/ ---' . PHP_EOL;
echo 'Rückgabe:    ' . var_export($ergebnis, true) . PHP_EOL; // false, kein 0
echo 'Fehlercode:  ' . preg_last_error_msg() . PHP_EOL;
echo 'ist false:   ' . ($ergebnis === false ? 'ja' : 'nein') . PHP_EOL;
echo 'Dauer:       ' . number_format($dauerMs, 1, ',', '.') . ' ms' . PHP_EOL;

// Wer nur auf "kein Treffer" prüft (== 0 oder falsy), hält den Fehler
// fälschlich für ein sauberes "passt nicht" - eine gefährliche Lücke.
echo 'naiv als "kein Treffer" gedeutet: '
    . ($ergebnis ? 'Treffer' : 'kein Treffer') . PHP_EOL;

echo PHP_EOL;

// --- Die Entschärfung ---------------------------------------------------
// Fix 1: den verschachtelten Quantor ganz vermeiden. /^a+$/ prüft dasselbe
// (eine nicht leere Folge von a) in linearer Zeit.
$einfach = '/^a+$/';

// Fix 2: eine atomare Gruppe verbietet das Zurückrollen des einmal
// Getroffenen. Damit ist die exponentielle Aufteilung von vornherein
// ausgeschlossen. Ihre Schreibweise steht im Muster unten.
$atomar = '/^(?>a+)+$/';

foreach (['einfach /^a+$/' => $einfach, 'atomar /^(?>a+)+$/' => $atomar] as $name => $muster) {
    $start    = hrtime(true);
    $ergebnis = preg_match($muster, $eingabe);
    $dauerMs  = (hrtime(true) - $start) / 1_000_000;

    echo str_pad($name, 20) . ' Rückgabe: ' . var_export($ergebnis, true)
        . ', Fehler: ' . preg_last_error_msg()
        . ', Dauer: ' . number_format($dauerMs, 3, ',', '.') . ' ms' . PHP_EOL;
}
