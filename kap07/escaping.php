<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 7: Ausgabe im Web absichern
 *
 * Vor der Ausgabe in HTML werden kritische Zeichen mit htmlspecialchars
 * in ihre harmlosen HTML-Entsprechungen umgewandelt. So wird aus einem
 * eingeschleusten Skript sichtbarer Text statt ausgeführter Code.
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

$eingabe = '<script>alert("hi")</script>';

// So niemals roh ins HTML schreiben. Erst absichern:
echo htmlspecialchars($eingabe, ENT_QUOTES) . "\n";
