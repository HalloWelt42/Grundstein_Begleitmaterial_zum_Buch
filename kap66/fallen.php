<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Wiederkehrende Fallen: der String-Vergleich von Daten, das Raten von
 * strtotime - und der ehrliche Gegenentwurf, der String-Konstruktor, der
 * kaputte Eingaben seit PHP 8.3 als typisierte Ausnahme meldet.
 */

$utc = new DateTimeZone('UTC');

// --- Falle 1: Objekte vergleichen, nicht formatierte Strings -------
echo '--- Vergleich ---' . PHP_EOL;
$a = new DateTimeImmutable('2026-01-30', $utc); // 30. Januar
$b = new DateTimeImmutable('2026-02-02', $utc); // 2. Februar (später)

// Richtig: die Objekte direkt vergleichen - über den echten Augenblick.
echo 'Objekt-Vergleich: ' . ($a < $b ? 'a vor b (richtig)' : 'a nach b') . PHP_EOL;

// Falsch: ein String-Vergleich im Format d.m.Y. "30.01..." ist als Text
// größer als "02.02...", weil der Tag vorne steht - die Reihenfolge kippt.
$sA = $a->format('d.m.Y');
$sB = $b->format('d.m.Y');
echo "String d.m.Y:     {$sA} < {$sB} ? "
    . ($sA < $sB ? 'ja' : 'nein (falsch!)') . PHP_EOL;

// --- Falle 2: strtotime rät bei mehrdeutigen Eingaben --------------
echo PHP_EOL . '--- strtotime rät ---' . PHP_EOL;
// "10/11/2026" mit Schrägstrichen liest strtotime amerikanisch als
// Monat/Tag/Jahr - hier also Oktober, nicht November.
echo '10/11/2026 -> ' . date('Y-m-d', strtotime('10/11/2026')) . PHP_EOL;
// Unsinn ergibt kommentarlos false - leicht zu übersehen.
var_dump(strtotime('nächsten kirchtag'));

// --- Falle 3: kaputte Eingabe - schweigen oder werfen --------------
echo PHP_EOL . '--- Kaputte Eingabe: schweigen oder werfen ---' . PHP_EOL;
// strtotime() liefert bei kaputter Eingabe kommentarlos false.
echo 'strtotime:   ' . var_export(strtotime('kein datum'), true) . PHP_EOL;

// Der String-Konstruktor wirft dagegen seit PHP 8.3 eine typisierte
// Ausnahme - dieser Fehler lässt sich nicht mehr übersehen.
try {
    new DateTimeImmutable('kein datum');
} catch (DateMalformedStringException $fehler) {
    echo 'Konstruktor: DateMalformedStringException' . PHP_EOL;
}

// Eine unbekannte Zeitzone wirft DateInvalidTimeZoneException (seit 8.3).
try {
    new DateTimeZone('Europa/Buxtehude');
} catch (DateInvalidTimeZoneException $fehler) {
    echo 'Zone:        DateInvalidTimeZoneException' . PHP_EOL;
}
