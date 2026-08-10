<?php

declare(strict_types=1);

use App\Http\BestellungController;
use App\Infrastructure\Container\Container;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Der Front Controller (Kapitel 43): der eine Eingang der Anwendung. Der
 * Webserver leitet jede Anfrage hierher. Diese Datei baut den Container an der
 * Kompositionswurzel auf, holt den Controller und schickt die Anfrage hindurch.
 * Ein bewusst winziges Routing genügt: nur POST /bestellungen ist belegt.
 *
 * Lokal starten (im Ordner public/):
 *   php -S 0.0.0.0:8080 index.php
 */
require __DIR__ . '/../vendor/autoload.php';

/** @var callable(?string): Container $bootstrap */
$bootstrap = require __DIR__ . '/../config/bootstrap.php';
$container = $bootstrap(__DIR__ . '/../.env');

$methode = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$pfad    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($methode === 'POST' && $pfad === '/bestellungen') {
    // Den JSON-Rumpf einlesen und an den Controller geben.
    $roh     = (string) file_get_contents('php://input');
    $eingabe = json_decode($roh, true);

    $controller = $container->get(BestellungController::class);
    $antwort    = $controller->aufgeben(is_array($eingabe) ? $eingabe : []);
} else {
    // Jeder andere Pfad ist eine schlichte 404.
    $antwort = ['status' => 404, 'body' => ['fehler' => 'Nicht gefunden']];
}

// Die einzige Stelle, die HTTP wirklich anfasst: Status, Header, Rumpf. Die
// Anwendung nutzt nur eine Handvoll Statuscodes; ihre Klartexte stehen hier.
$klartext = [201 => 'Created', 404 => 'Not Found', 422 => 'Unprocessable Content'];
$status   = $antwort['status'];

header(sprintf('HTTP/1.1 %d %s', $status, $klartext[$status] ?? 'OK'));
header('Content-Type: application/json; charset=utf-8');
echo json_encode(
    $antwort['body'],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
) . PHP_EOL;
