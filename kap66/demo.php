<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Testbare Zeit mit PSR-20. Im Betrieb liefert die SystemClock die echte
 * Zeit in UTC; für eine wiederholbare Ausgabe treibt hier eine FesteClock
 * den Gutscheindienst an. Der Dienst kennt nur den Vertrag ClockInterface
 * und merkt vom Unterschied nichts.
 */

use App\FesteClock;
use App\Gutscheindienst;
use App\SystemClock;

$utc = new DateTimeZone('UTC');

// --- Betrieb: die echte Uhr liefert immer UTC ----------------------
$system = new SystemClock();
echo 'Betriebsuhr-Zone: ' . $system->now()->getTimezone()->getName() . PHP_EOL;

// --- Deterministisch: eine feste Uhr für wiederholbare Ausgabe -----
$fest = new FesteClock(new DateTimeImmutable('2026-08-10 09:00:00', $utc));
$dienst = new Gutscheindienst($fest);

$gutschein = $dienst->stelleAus(2500);
echo PHP_EOL . 'Ausgestellt: ' . $gutschein->ausgestelltAm->format('Y-m-d H:i') . PHP_EOL;
echo 'Gültig bis:  ' . $gutschein->gueltigBis->format('Y-m-d H:i') . PHP_EOL;

// Gültigkeit gegen bekannte Zeitpunkte geprüft - vollständig deterministisch.
$amAblauftag = new DateTimeImmutable('2026-09-09 09:00:00', $utc);
$einTagSpaeter = new DateTimeImmutable('2026-09-10 09:00:00', $utc);
echo 'Am Ablauftag gültig:     ' . ($gutschein->istGueltigAm($amAblauftag) ? 'ja' : 'nein') . PHP_EOL;
echo 'Einen Tag danach gültig: ' . ($gutschein->istGueltigAm($einTagSpaeter) ? 'ja' : 'nein') . PHP_EOL;
