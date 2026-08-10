<?php

declare(strict_types=1);

/*
 * Der Router ist reine Logik - kein Webserver nötig, um ihn zu prüfen.
 * Genau wie beim Request-Objekt aus Kapitel 36 füttern wir ihn mit
 * eigenen Werten und schauen, was match() zurückgibt.
 *
 * Aufruf: docker run --rm -v "$PWD":/app -w /app php:8.4-cli php test-router.php
 */

require __DIR__ . '/src/Http/Request.php';
require __DIR__ . '/src/Http/Response.php';
require __DIR__ . '/src/Mini/RouteStatus.php';
require __DIR__ . '/src/Mini/Route.php';
require __DIR__ . '/src/Mini/RouteResult.php';
require __DIR__ . '/src/Mini/Router.php';

use Grundstein\Http\Request;
use Grundstein\Http\Response;
use Grundstein\Mini\Router;
use Grundstein\Mini\RouteStatus;

$router = new Router();
$router->get('/kunden/{id}', fn (Request $r, array $p): Response => new Response());
$router->post('/kunden', fn (Request $r, array $p): Response => new Response());

// Treffer: Pfad und Methode passen, der Parameter fällt ab.
$treffer = $router->match('GET', '/kunden/7');
echo 'GET /kunden/7  -> ' . $treffer->status->name;
echo ', id=' . $treffer->params['id'] . PHP_EOL;

// Nicht erlaubte Methode: Pfad passt, GET nicht - Allow nennt POST.
$m405 = $router->match('GET', '/kunden');
echo 'GET /kunden    -> ' . $m405->status->name;
echo ', Allow=' . implode(', ', $m405->allowedMethods) . PHP_EOL;

// Nichts passt.
$m404 = $router->match('GET', '/gibtsnicht');
echo 'GET /gibtsnicht-> ' . $m404->status->name . PHP_EOL;

// Prüfen, dass der Status wirklich das erwartete Enum ist.
var_dump($treffer->status === RouteStatus::Found);
