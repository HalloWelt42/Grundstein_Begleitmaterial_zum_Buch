<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 35: Statuscode und Location-Header bewusst setzen.
 *
 * Eine alte Adresse ist umgezogen. Statt einer Seite schickt das Skript
 * eine Weiterleitung: den Statuscode 301 (dauerhaft verschoben) und im
 * Location-Header das neue Ziel. Der Browser folgt dieser Angabe von
 * selbst und ruft die neue Adresse auf.
 */

// http_response_code setzt die Statuszeile der Antwort. 301 bedeutet:
// diese Ressource ist dauerhaft an eine andere Adresse umgezogen.
http_response_code(301);

// Der Location-Header nennt das neue Ziel. Erst zusammen mit einem
// 3xx-Status wird er als Weiterleitung verstanden.
header('Location: /hallo-http.php');

// Ein Rumpf ist bei einer Weiterleitung optional; viele Browser folgen
// dem Location-Header sofort und zeigen ihn nie an. Für Werkzeuge, die
// nicht automatisch folgen, hinterlassen wir eine kurze Notiz.
echo 'Diese Seite ist umgezogen: /hallo-http.php';
