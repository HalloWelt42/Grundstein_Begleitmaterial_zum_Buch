<?php

declare(strict_types=1);

require __DIR__ . '/Request.php';
require __DIR__ . '/Response.php';

use Grundstein\Http\Request;
use Grundstein\Http\Response;

// Die einzige Stelle, die die Superglobals liest. Ab hier arbeitet das
// Programm nur noch mit dem getippten Request-Objekt.
$request = Request::fromGlobals();
$antwort = new Response();

// Fehlt der Name ganz, leiten wir auf eine sinnvolle Vorgabe um.
if ($request->query('name') === null) {
    $antwort->redirect($request->path() . '?name=Welt')->send();

    return;
}

$name = $request->query('name', 'Welt');

// Das Alter ist optional. Ist es angegeben, muss es eine Ganzzahl sein;
// sonst antworten wir mit 400 (Bad Request) statt mit einer halben Seite.
$alterRoh = $request->query('alter');
$alter = $request->queryInt('alter');

if ($alterRoh !== null && $alter === null) {
    $antwort
        ->status(400)
        ->body("<p>Das Alter muss eine ganze Zahl sein.</p>\n")
        ->send();

    return;
}

// Jeder Nutzerwert wird beim Einbau ins HTML escaped - hier der Name.
$sichererName = htmlspecialchars($name, ENT_QUOTES);
$alterZeile = $alter === null
    ? ''
    : "    <p>Du bist {$alter} Jahre alt.</p>\n";

$html = "<!DOCTYPE html>\n"
    . "<html lang=\"de\">\n"
    . "<head><meta charset=\"utf-8\"><title>Begrüßung</title></head>\n"
    . "<body>\n"
    . "    <h1>Hallo, {$sichererName}!</h1>\n"
    . $alterZeile
    . "</body>\n"
    . "</html>\n";

$antwort->status(200)->body($html)->send();
