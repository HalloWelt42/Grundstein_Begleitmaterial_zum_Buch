<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Zeitspannen (DateInterval), Differenzen (diff) und Zeiträume in
 * Schritten (DatePeriod).
 */

$utc = new DateTimeZone('UTC');
$von = new DateTimeImmutable('2026-01-01 00:00:00', $utc);

// --- DateInterval: eine feste Zeitspanne addieren ------------------
echo '--- Zeitspanne addieren ---' . PHP_EOL;
// P = Periode, dann Datum (1 Jahr, 2 Monate, 10 Tage). Zeitanteile
// stehen hinter einem T, etwa PT2H30M für zwei Stunden, dreißig Minuten.
$plus = $von->add(new DateInterval('P1Y2M10D'));
echo $von->format('Y-m-d') . ' + P1Y2M10D = ' . $plus->format('Y-m-d') . PHP_EOL;

// --- diff(): der Abstand zweier Zeitpunkte -------------------------
echo PHP_EOL . '--- Differenz ---' . PHP_EOL;
$ziel = new DateTimeImmutable('2026-12-31 00:00:00', $utc);
$abstand = $von->diff($ziel);
// %a = Gesamttage, %m/%d = Monate und Resttage der Zerlegung.
echo $abstand->format('%a Tage gesamt (%m Monate, %d Tage)') . PHP_EOL;
echo 'Richtung: ' . ($abstand->invert === 1 ? 'rückwärts' : 'vorwärts') . PHP_EOL;

// --- DatePeriod: ein Zeitraum in gleichmäßigen Schritten -----------
echo PHP_EOL . '--- Zeitraum in Wochenschritten (Ende ausschließlich) ---' . PHP_EOL;
$eineWoche = new DateInterval('P7D');
$bis = new DateTimeImmutable('2026-02-01 00:00:00', $utc);
foreach (new DatePeriod($von, $eineWoche, $bis) as $tag) {
    echo $tag->format('Y-m-d') . PHP_EOL;
}
