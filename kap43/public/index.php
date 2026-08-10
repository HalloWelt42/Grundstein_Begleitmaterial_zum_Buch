<?php

declare(strict_types=1);

/*
 * Der Front Controller: der eine Einstiegspunkt, an den der Webserver
 * jede Anfrage leitet. Er lädt die Klassen, definiert die Routen, baut die
 * Pipeline und schickt die Antwort hinaus. Mehr Anwendungslogik steht hier
 * bewusst nicht - die lebt in den Handlern.
 *
 * Start mit dem eingebauten Server aus dem Ordner public/:
 *   php -S 0.0.0.0:8080 index.php
 * Die als Router-Skript übergebene index.php wird für JEDE URL aufgerufen -
 * genau das, was ein echter Server per Rewrite-Regel erledigt.
 */

// Ein kleiner PSR-4-Autoloader: aus "Grundstein\Mini\Router" wird der
// Pfad ../src/Mini/Router.php. Kein Composer nötig, das Prinzip aber gleich.
spl_autoload_register(function (string $klasse): void {
    $prefix = 'Grundstein\\';
    if (!str_starts_with($klasse, $prefix)) {
        return;
    }

    $rest = substr($klasse, strlen($prefix));
    $pfad = __DIR__ . '/../src/' . str_replace('\\', '/', $rest) . '.php';

    if (is_file($pfad)) {
        require $pfad;
    }
});

use Grundstein\Http\Request;
use Grundstein\Http\Response;
use Grundstein\Mini\Dispatcher;
use Grundstein\Mini\Pipeline;
use Grundstein\Mini\Router;
use Grundstein\Mini\SecurityHeadersMiddleware;
use Grundstein\Mini\ShowKundeHandler;

// 1. Routen anmelden. Handler sind Closures oder aufrufbare Objekte.
$router = new Router();

$router->get('/', function (Request $request, array $params): Response {
    $html = "<!DOCTYPE html>\n"
        . "<html lang=\"de\"><head><meta charset=\"utf-8\">"
        . "<title>Mini-Framework</title></head>\n"
        . "<body>\n"
        . "    <h1>Willkommen</h1>\n"
        . "    <p>Versuche <code>/kunden/1</code> oder <code>/kunden/2</code>.</p>\n"
        . "</body></html>\n";

    return (new Response())->body($html);
});

// Route mit Pfadparameter, bedient von einer aufrufbaren Klasse.
$router->get('/kunden/{id}', new ShowKundeHandler());

// Dieselbe Ressource, andere Methode. Ein GET auf /kunden liefert 405.
$router->post('/kunden', function (Request $request, array $params): Response {
    return (new Response())
        ->status(201)
        ->json(['angelegt' => true]);
});

// 2. Kern und Pipeline verdrahten. Der Dispatcher ist der innerste
//    Handler, die Middleware liegt davor.
$dispatcher = new Dispatcher($router);
$pipeline = new Pipeline([new SecurityHeadersMiddleware()], $dispatcher);

// 3. Anfrage lesen, durch die Pipeline schicken, Antwort ausliefern.
$response = $pipeline->handle(Request::fromGlobals());
$response->send();
