<?php

declare(strict_types=1);

require __DIR__ . '/Request.php';

use Grundstein\Http\Request;

$request = new Request(
    query: ['name' => 'Ada', 'alter' => '39'],
    server: ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/suche?q=php'],
);

echo 'Methode: ' . $request->method() . PHP_EOL;
echo 'Pfad: ' . $request->path() . PHP_EOL;
echo 'Name: ' . $request->query('name', 'Welt') . PHP_EOL;
echo 'Stadt: ' . $request->query('stadt', 'unbekannt') . PHP_EOL;

// Ein gültiges Alter kommt als echter int zurück ...
var_dump($request->queryInt('alter'));

// ... ein fehlendes oder unsinniges als null.
$kaputt = new Request(query: ['alter' => 'dreißig']);
var_dump($kaputt->queryInt('alter'));
