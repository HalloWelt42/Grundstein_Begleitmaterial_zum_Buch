<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Ein und derselbe Augenblick, in verschiedenen Zonen betrachtet. Der
 * Augenblick wird in UTC gehalten; setTimezone() ändert nur die Anzeige,
 * nicht den Zeitpunkt. Am Ende: der Sommerzeit-Sprung, bei dem eine
 * Ortszeit schlicht nicht existiert.
 */

// Ein Augenblick, festgehalten in UTC - so speichert man ihn.
$augenblick = new DateTimeImmutable('2026-07-01 12:00:00', new DateTimeZone('UTC'));

echo '--- Derselbe Augenblick in vier Zonen ---' . PHP_EOL;
foreach (['UTC', 'Europe/Berlin', 'America/New_York', 'Asia/Tokyo'] as $zone) {
    // setTimezone() rechnet nur die Anzeige um, der Augenblick bleibt gleich.
    $lokal = $augenblick->setTimezone(new DateTimeZone($zone));
    printf("%-18s %s%s", $zone, $lokal->format('Y-m-d H:i P'), PHP_EOL);
}

// Beweis: Es ist wirklich derselbe Zeitpunkt - der Unix-Zeitstempel
// (Sekunden seit 1970 in UTC) ist in allen Zonen identisch.
echo PHP_EOL . 'Gleicher Zeitstempel überall: ' . $augenblick->getTimestamp() . PHP_EOL;

echo PHP_EOL . '--- Sommerzeit-Sprung (Europe/Berlin) ---' . PHP_EOL;
$berlin = new DateTimeZone('Europe/Berlin');
// In der Nacht zum 29.03.2026 springt die Uhr von 02:00 direkt auf 03:00;
// die Ortszeit 02:30 gibt es an diesem Tag gar nicht.
$vor = new DateTimeImmutable('2026-03-29 01:30:00', $berlin);
$nach = $vor->add(new DateInterval('PT1H')); // eine Stunde echter Zeit später
echo 'Vorher:  ' . $vor->format('H:i P') . PHP_EOL;
echo 'Plus 1h: ' . $nach->format('H:i P') . PHP_EOL;
