<?php

declare(strict_types=1);

use App\Application;

/*
 * Der einzige öffentlich erreichbare Einstiegspunkt (Front Controller).
 * Er liegt allein in public/; der übrige Code in src/ bleibt außerhalb
 * der Web-Wurzel und ist vom Browser nicht erreichbar.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

// Konfiguration kommt aus der Umgebung, nie aus dem Image (Kapitel 57).
$app = new Application(
    version: getenv('APP_VERSION') ?: 'unbekannt',
    umgebung: getenv('APP_ENV') ?: 'prod',
);

$pfad = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

$antwort = $app->beantworte($pfad);

http_response_code($antwort['code']);
header('Content-Type: text/plain; charset=utf-8');

echo $antwort['text'] . PHP_EOL;
