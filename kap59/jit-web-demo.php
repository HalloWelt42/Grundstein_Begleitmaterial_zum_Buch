<?php

declare(strict_types=1);

/*
 * Der Gegenpol zum Mandelbrot-Kern: eine Last, wie sie eine typische
 * Web-Anfrage trägt - Arrays umbauen, Zeichenketten zusammensetzen,
 * formatieren. Kaum reine Zahlen-Arithmetik. An diesem Code hat der JIT
 * wenig zu gewinnen, und genau das soll die Messung ehrlich zeigen.
 */

/**
 * Baut aus vielen Datensätzen eine HTML-Tabelle als Zeichenkette - stark
 * vereinfacht, aber vom Charakter her die Arbeit einer Web-Anfrage: Arrays
 * durchlaufen, Werte formatieren, Zeichenketten verketten.
 *
 * @param list<array{name: string, betrag: int}> $zeilen
 */
function baueTabelle(array $zeilen): string
{
    $teile = [];
    foreach ($zeilen as $zeile) {
        $name = htmlspecialchars($zeile['name'], ENT_QUOTES);
        $betrag = number_format($zeile['betrag'] / 100, 2, ',', '.');
        $teile[] = "  <tr><td>{$name}</td><td>{$betrag} Euro</td></tr>";
    }

    return "<table>\n" . implode("\n", $teile) . "\n</table>";
}

// Testdaten: viele kleine Datensätze, wie sie aus einer Datenbank kämen.
$zeilen = [];
for ($i = 1; $i <= 5000; $i++) {
    $zeilen[] = ['name' => "Kunde {$i}", 'betrag' => $i * 137];
}

$status = function_exists('opcache_get_status') ? opcache_get_status(false) : false;
$jitAn = is_array($status) && (bool) ($status['jit']['on'] ?? false);

$start = hrtime(true);
$laenge = 0;
for ($runde = 0; $runde < 200; $runde++) {   // 200-mal, damit die Messung stabil wird
    $laenge = strlen(baueTabelle($zeilen));
}
$dauer = (hrtime(true) - $start) / 1_000_000; // ms

printf('JIT aktiv:  %s%s', $jitAn ? 'ja' : 'nein', PHP_EOL);
printf('Zeichen:    %d%s', $laenge, PHP_EOL);
printf('Dauer:      %.1f ms%s', $dauer, PHP_EOL);
