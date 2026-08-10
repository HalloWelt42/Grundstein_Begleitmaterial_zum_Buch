<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use Grundstein\Http\AuthMiddleware;
use Grundstein\Http\BegruessungsHandler;
use Grundstein\Http\LoggingMiddleware;
use Grundstein\Http\Pipeline;
use Grundstein\Http\Protokoll;
use Grundstein\Http\ServerRequest;

// Ein Protokoll, das die Reihenfolge der Schalen sichtbar macht.
$protokoll = new Protokoll();

// Die Kette von außen nach innen: erst Logging, dann Anmeldung, dann
// der Kern-Handler, der die eigentliche Antwort baut.
$pipeline = new Pipeline(
    middleware: [
        new LoggingMiddleware($protokoll),
        new AuthMiddleware('geheim'),
    ],
    kern: new BegruessungsHandler(),
);

// Fall 1: gültiges Token - die Anfrage läuft bis zum Kern durch.
$mitToken = new ServerRequest(
    method: 'GET',
    path: '/gruss',
    headers: ['X-Token' => 'geheim'],
);
$antwort = $pipeline->handle($mitToken);

echo 'Fall 1 - gültiges Token' . PHP_EOL;
echo "  Status: {$antwort->getStatusCode()} {$antwort->getReasonPhrase()}" . PHP_EOL;
echo '  Rumpf:  ' . trim($antwort->getBody()) . PHP_EOL;

// Fall 2: falsches Token - die AuthMiddleware bricht nach innen ab.
$ohneToken = new ServerRequest(
    method: 'GET',
    path: '/gruss',
    headers: ['X-Token' => 'falsch'],
);
$abgelehnt = $pipeline->handle($ohneToken);

echo PHP_EOL . 'Fall 2 - falsches Token' . PHP_EOL;
echo "  Status: {$abgelehnt->getStatusCode()} {$abgelehnt->getReasonPhrase()}" . PHP_EOL;
echo '  Rumpf:  ' . trim($abgelehnt->getBody()) . PHP_EOL;

// Das Protokoll zeigt die Zwiebel: Logging umschließt beide Fälle, und
// im zweiten fehlt der Kern, weil die Anmelde-Schale vorher abbricht.
echo PHP_EOL . 'Protokoll (Reihenfolge der Schalen):' . PHP_EOL;
foreach ($protokoll->zeilen() as $zeile) {
    echo '  ' . $zeile . PHP_EOL;
}
