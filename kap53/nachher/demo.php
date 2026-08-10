<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Die Verdrahtung am Rand der Anwendung. Nur hier - an einer einzigen,
 * bewusst dummen Stelle - werden die Schichten zusammengesteckt: die
 * PDO-Verbindung, das Repository (Infrastruktur), der Anwendungsdienst
 * und der Controller (Präsentation). Danach läuft dieselbe Anfrage
 * durch die volle Kette. Wie ein Container diese Verdrahtung abnimmt,
 * zeigt der Rest von Teil VIII.
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Http\AnmeldeController;
use App\Application\AnmeldeService;
use App\Infrastructure\PdoAbonnentRepository;
use App\Infrastructure\SystemClock;

// SQLite im Arbeitsspeicher - kein Server, keine Datei (Kapitel 31).
$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
$pdo->exec(
    'CREATE TABLE abonnent (
        id            INTEGER PRIMARY KEY,
        email         TEXT NOT NULL UNIQUE,
        angemeldet_am TEXT NOT NULL
    )'
);

// Die Schichten von innen nach außen verdrahten.
$repository = new PdoAbonnentRepository($pdo);
$service    = new AnmeldeService($repository, new SystemClock());
$controller = new AnmeldeController($service);

// Drei simulierte Formular-Absendungen durch die volle Schichtenkette.
foreach (['ada@example.org', 'ada@example.org', 'kein-at-zeichen'] as $eingabe) {
    $antwort = $controller->anmelden(['email' => $eingabe]);
    echo "{$antwort->status}: {$antwort->rumpf}" . PHP_EOL;
}
