<?php

declare(strict_types=1);

// Zeigt, wie eine ungefangene Ausnahme aussieht: Typ, Meldung, Datei und
// Zeile des throw, dann der Stack Trace als Rückreise durch die Aufrufkette.
// Bewusst NICHT gefangen, damit PHP die vollständige Meldung druckt.

function ladeKonfig(string $pfad): array
{
    $roh = leseDatei($pfad);   // eine Ebene tiefer geht es schief

    return parseKonfig($roh);
}

function leseDatei(string $pfad): string
{
    $inhalt = @file_get_contents($pfad);
    if ($inhalt === false) {
        throw new RuntimeException("Datei nicht lesbar: {$pfad}");
    }

    return $inhalt;
}

function parseKonfig(string $roh): array
{
    return parse_ini_string($roh, true) ?: [];
}

ladeKonfig('/gibt/es/nicht.ini');
