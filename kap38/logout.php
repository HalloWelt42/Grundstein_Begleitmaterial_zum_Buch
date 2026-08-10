<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 38: Abmelden.
 *
 * Ein sauberes Logout hat drei Schritte: die Session-Daten leeren, das
 * Session-Cookie im Browser ungültig machen und die Session auf dem
 * Server zerstören. Erst alle drei zusammen beenden die Sitzung wirklich.
 */

require __DIR__ . '/anmeldung.php';

sichere_session_starten();

// 1. Alle Daten der Session im Speicher leeren.
$_SESSION = [];

// 2. Das Session-Cookie im Browser ungültig machen (Ablauf in der
//    Vergangenheit). Ohne diesen Schritt bliebe die ID im Browser.
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 42000,
        'path'     => $p['path'],
        'domain'   => $p['domain'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'],
    ]);
}

// 3. Die Session serverseitig zerstören.
session_destroy();

header('Location: login.php', true, 303);
exit;
