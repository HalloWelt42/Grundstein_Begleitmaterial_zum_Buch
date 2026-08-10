<?php

declare(strict_types=1);

/*
 * Der Front Controller der JSON-API. Er lädt die Klassen, öffnet die
 * Datenbank, verdrahtet Repository und Controller, meldet die Routen der
 * Ressource "Kunde" an und schickt jede Anfrage durch die Pipeline.
 *
 * Start mit dem eingebauten Server aus dem Ordner public/:
 *   php -S 0.0.0.0:8080 index.php
 * Die als Router-Skript übergebene index.php wird für JEDE URL aufgerufen.
 */

// Ein kleiner PSR-4-Autoloader: aus "Grundstein\Kunden\Kunde" wird der
// Pfad ../src/Kunden/Kunde.php. Kein Composer nötig, das Prinzip aus
// Kapitel 18 aber gleich.
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

use Grundstein\Api\JsonDispatcher;
use Grundstein\Api\JsonErrorMiddleware;
use Grundstein\Http\Request;
use Grundstein\Kunden\Datenbank;
use Grundstein\Kunden\KundenController;
use Grundstein\Kunden\PdoKundenRepository;
use Grundstein\Mini\Pipeline;
use Grundstein\Mini\Router;

// 1. Datenbank öffnen und die Kette Repository -> Controller aufbauen.
$pdo = Datenbank::verbinden(__DIR__ . '/../var/kunden.sqlite');
$repository = new PdoKundenRepository($pdo);
$controller = new KundenController($repository);

// 2. Routen der Ressource anmelden. Jede Methode des Controllers wird als
//    First-Class-Callable übergeben - der Router ruft sie wie eine
//    Funktion auf.
$router = new Router();
$router->get('/kunden', $controller->index(...));
$router->post('/kunden', $controller->create(...));
$router->get('/kunden/{id}', $controller->show(...));
$router->add('PUT', '/kunden/{id}', $controller->update(...));
$router->add('DELETE', '/kunden/{id}', $controller->delete(...));

// 3. Pipeline: die Fehler-Middleware außen, der JSON-Dispatcher im Kern.
$pipeline = new Pipeline([new JsonErrorMiddleware()], new JsonDispatcher($router));

// 4. Anfrage lesen, durch die Pipeline schicken, Antwort ausliefern.
$pipeline->handle(Request::fromGlobals())->send();
