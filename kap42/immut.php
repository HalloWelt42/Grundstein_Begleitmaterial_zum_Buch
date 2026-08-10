<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use Grundstein\Http\Response;

// Zeigt die Unveränderlichkeit: withStatus() ändert das Original NICHT,
// sondern liefert eine veränderte Kopie zurück.
$antwort = new Response();                     // Status 200
$neu = $antwort->withStatus(404, 'Not Found'); // eine KOPIE mit 404

echo 'antwort: ' . $antwort->getStatusCode() . PHP_EOL;
echo 'neu:     ' . $neu->getStatusCode() . PHP_EOL;

// Es sind zwei verschiedene Objekte - das Original blieb unberührt.
var_dump($antwort === $neu);
