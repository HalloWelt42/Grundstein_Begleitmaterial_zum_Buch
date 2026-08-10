<?php

declare(strict_types=1);

// Führt den ganzen Lebensweg eines Auftrags vor: reservieren, erledigen,
// scheitern, wiederholen und schließlich als toter Auftrag aufgeben.
// Als Datenbank dient SQLite im Arbeitsspeicher - kein Server nötig.

require __DIR__ . '/vendor/autoload.php';

use App\Datenbank;
use App\Queue;

$queue = new Queue(Datenbank::oeffne(':memory:'));
$queue->migriere();

// Zwei Aufträge: der zweite darf höchstens zweimal versucht werden.
$queue->lege('email', ['an' => 'ada@example.org', 'betreff' => 'Willkommen']);
$queue->lege('bild', ['datei' => 'kaputt.jpg'], maxVersuche: 2);

echo 'Nach dem Einstellen: offen=' . $queue->zaehle('offen') . PHP_EOL;

// Erster Auftrag: reservieren und erfolgreich erledigen.
$a = $queue->reserviere();
echo "Reserviert: #{$a->id} ({$a->typ}), Versuch {$a->versuch}" . PHP_EOL;
echo '  in Arbeit: ' . $queue->zaehle('in_arbeit') . PHP_EOL;
$queue->erledige($a->id);
echo '  nach erledige(): erledigt=' . $queue->zaehle('erledigt') . PHP_EOL;

// Zweiter Auftrag: erster Fehlversuch - er landet wieder auf "offen".
$b = $queue->reserviere();
echo "Reserviert: #{$b->id} ({$b->typ}), Versuch {$b->versuch}" . PHP_EOL;
$queue->meldeFehler($b->id, 'Bild nicht lesbar');
echo '  nach 1. Fehler: offen=' . $queue->zaehle('offen')
    . ', fehlgeschlagen=' . $queue->zaehle('fehlgeschlagen') . PHP_EOL;

// Zweiter Auftrag erneut: zweiter Fehlversuch - jetzt ist die Grenze erreicht.
$c = $queue->reserviere();
echo "Reserviert: #{$c->id} ({$c->typ}), Versuch {$c->versuch}" . PHP_EOL;
$queue->meldeFehler($c->id, 'Bild nicht lesbar');
echo '  nach 2. Fehler: offen=' . $queue->zaehle('offen')
    . ', fehlgeschlagen=' . $queue->zaehle('fehlgeschlagen') . PHP_EOL;

// Nichts mehr offen: reserviere() liefert null.
$leer = $queue->reserviere();
echo 'Ende: reserviere() liefert '
    . ($leer === null ? 'null (nichts mehr offen)' : 'wider Erwarten einen Auftrag')
    . PHP_EOL;
