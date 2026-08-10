<?php

declare(strict_types=1);

// Mit dem Flag JSON_THROW_ON_ERROR meldet json_decode() einen Fehler als
// JsonException, statt still null zurückzugeben und den Fehler zu verstecken.

$kaputt = '{"name": "Ada",}';   // ein Komma zu viel vor der Klammer

try {
    $daten = json_decode($kaputt, true, 512, JSON_THROW_ON_ERROR);
} catch (\JsonException $fehler) {
    echo 'JsonException: ' . $fehler->getMessage() . "\n";
}

// Korrektur: gültiges JSON dekodieren - dann kommt sauber ein Array zurück.
$daten = json_decode('{"name": "Ada"}', true, 512, JSON_THROW_ON_ERROR);
echo 'Name: ' . $daten['name'] . "\n";
