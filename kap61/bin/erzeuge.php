<?php

declare(strict_types=1);

// Der Erzeuger: stellt ein paar Beispielaufträge in die Warteschlange.
// Genau das täte der Web-Prozess, bevor er dem Nutzer sofort antwortet -
// er notiert die Arbeit und ist fertig.

require __DIR__ . '/../vendor/autoload.php';

use App\Datenbank;
use App\Queue;

$pfad = $argv[1] ?? __DIR__ . '/../auftraege.sqlite';

$queue = new Queue(Datenbank::oeffne($pfad));
$queue->migriere();

// Ein paar Beispielaufträge einstellen.
$auftraege = [
    ['email',   ['an' => 'ada@example.org',   'betreff' => 'Willkommen']],
    ['bild',    ['datei' => 'urlaub.jpg',      'kante' => 800]],
    ['email',   ['an' => 'grace@example.org',  'betreff' => 'Willkommen']],
    ['bericht', ['monat' => '2026-07']],
    ['bild',    ['datei' => 'strand.jpg',      'kante' => 800]],
];

foreach ($auftraege as [$typ, $daten]) {
    $id = $queue->lege($typ, $daten);
    echo "Eingestellt: #{$id} ({$typ})" . PHP_EOL;
}

echo 'Offen in der Warteschlange: ' . $queue->zaehle('offen') . PHP_EOL;
