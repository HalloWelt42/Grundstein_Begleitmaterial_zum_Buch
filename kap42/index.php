<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use Grundstein\Http\AuthMiddleware;
use Grundstein\Http\BegruessungsHandler;
use Grundstein\Http\LoggingMiddleware;
use Grundstein\Http\Pipeline;
use Grundstein\Http\Protokoll;
use Grundstein\Http\ServerRequest;

// Ein winziger Front Controller: Er liest die echte Anfrage, schickt sie
// durch dieselbe Pipeline wie das CLI-Beispiel und gibt die Antwort aus.
$protokoll = new Protokoll();

$pipeline = new Pipeline(
    middleware: [
        new LoggingMiddleware($protokoll),
        new AuthMiddleware('geheim'),
    ],
    kern: new BegruessungsHandler(),
);

// fromGlobals() ist auch hier die einzige Stelle, die die Superglobals
// berührt - ab da arbeitet alles mit dem getippten Request-Objekt.
$antwort = $pipeline->handle(ServerRequest::fromGlobals());

$antwort->send();
