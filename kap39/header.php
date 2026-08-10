<?php

declare(strict_types=1);

/**
 * Setzt einige sicherheitsrelevante HTTP-Kopfzeilen, bevor auch nur ein
 * Byte Inhalt ausgegeben wird. header() muss vor jeder Ausgabe stehen,
 * sonst sind die Kopfzeilen schon unterwegs.
 *
 * Prüfen mit dem eingebauten Webserver und curl:
 *   docker run --rm -p 127.0.0.1:8080:8080 -v "$PWD":/app -w /app \
 *       php:8.4-cli php -S 0.0.0.0:8080
 *   curl -i "http://localhost:8080/kap39/header.php"
 */

// Das Format des Rumpfs samt Zeichenkodierung - damit Umlaute wie ä ö ü
// richtig ankommen und der Browser den Inhalt korrekt deutet.
header('Content-Type: text/html; charset=UTF-8');

// Verbietet dem Browser, den angegebenen Content-Type zu "erraten".
// So kann eine als Text ausgelieferte Datei nicht als Skript enden.
header('X-Content-Type-Options: nosniff');

// Content-Security-Policy schränkt ein, woher Skripte und andere Inhalte
// geladen werden dürfen - hier: nur von der eigenen Herkunft.
header("Content-Security-Policy: default-src 'self'");

// Strict-Transport-Security erzwingt HTTPS für künftige Aufrufe. Nur
// über eine verschlüsselte Verbindung sinnvoll - hier zur Anschauung.
header('Strict-Transport-Security: max-age=31536000');

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Sichere Kopfzeilen</title>
</head>
<body>
    <p>Diese Seite wird mit sicheren HTTP-Kopfzeilen ausgeliefert.</p>
</body>
</html>
