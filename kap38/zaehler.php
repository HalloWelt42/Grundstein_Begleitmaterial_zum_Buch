<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 38: Zustand in der Session halten.
 *
 * Ein winziger Besuchszähler, der über mehrere Anfragen hinweg
 * mitzählt. Anders als ein Cookie liegt der Wert nicht im Browser,
 * sondern serverseitig in der Session. Der Browser hält nur die
 * Session-ID, über die PHP die gespeicherten Daten wiederfindet.
 */

// session_start() muss vor jeder Ausgabe stehen: Beim ersten Aufruf
// legt es eine Session an und schickt die Session-ID als Cookie, bei
// jedem weiteren liest es die Daten zur mitgeschickten ID ein. Die
// Optionen setzen die sicheren Cookie-Flags direkt für dieses Cookie.
session_start([
    'cookie_httponly' => true,                             // kein JS-Zugriff auf die ID
    'cookie_secure'   => ($_SERVER['HTTPS'] ?? '') !== '', // nur über HTTPS
    'cookie_samesite' => 'Lax',                            // schützt vor Fremd-POST
    'use_strict_mode' => true,                             // keine fremd erfundenen IDs
]);

// $_SESSION ist ein gewöhnliches Array - nur dass sein Inhalt den
// nächsten Aufruf überlebt, solange die Session-ID mitkommt.
$_SESSION['zaehler'] = ($_SESSION['zaehler'] ?? 0) + 1;
$anzahl = $_SESSION['zaehler'];

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Besuchszähler</title>
</head>
<body>
    <h1>Willkommen zurück</h1>
    <p>Du hast diese Seite in dieser Sitzung <strong><?= $anzahl ?></strong>-mal aufgerufen.</p>
</body>
</html>
