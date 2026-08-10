<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 38: Ein Cookie sicher setzen und wieder lesen.
 *
 * Diese Seite merkt sich eine harmlose Voreinstellung - die gewünschte
 * Farbgebung - in einem Cookie. Das zeigt den vollständigen Kreislauf:
 * Der Server schickt mit setcookie() einen Set-Cookie-Header, der
 * Browser speichert den Wert und sendet ihn bei jeder weiteren Anfrage
 * im Cookie-Header zurück, wo PHP ihn in $_COOKIE bereitstellt.
 */

/**
 * Kurzform für die sichere HTML-Ausgabe. Jeder Wert aus einer
 * Nutzereingabe - auch aus einem Cookie - läuft durch diese Funktion,
 * bevor er ins HTML kommt.
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Ist die Verbindung verschlüsselt? Nur dann darf das Secure-Flag
// gesetzt werden - sonst würde der Browser das Cookie über eine
// unverschlüsselte http-Verbindung gar nicht erst zurücksenden.
$ueberHttps = ($_SERVER['HTTPS'] ?? '') !== '';

// Beim Absenden des Formulars ein Cookie mit sicheren Flags setzen.
// Wichtig: setcookie() schreibt einen Header und muss deshalb VOR jeder
// HTML-Ausgabe stehen.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nur zwei Werte sind erlaubt - wir vertrauen der Eingabe nicht.
    $farbe = ($_POST['farbe'] ?? '') === 'dunkel' ? 'dunkel' : 'hell';

    setcookie('theme', $farbe, [
        'expires'  => time() + 60 * 60 * 24 * 30, // 30 Tage haltbar
        'path'     => '/',                         // für die ganze Seite
        'httponly' => true,                        // kein Zugriff aus JavaScript
        'secure'   => $ueberHttps,                 // nur über HTTPS mitschicken
        'samesite' => 'Lax',                       // nicht bei fremden POST-Anfragen
    ]);

    // Post/Redirect/Get: nach dem Setzen zurück auf die reine GET-Seite.
    // So zeigt ein Neuladen nicht die POST-Antwort erneut an.
    header('Location: cookie.php', true, 303);
    exit;
}

// Bei einem GET liegt ein zuvor gesetztes Cookie in $_COOKIE bereit.
// Fehlt es (erster Besuch), greift der Standardwert.
$aktuell = (string) ($_COOKIE['theme'] ?? 'hell');

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Voreinstellung im Cookie</title>
</head>
<body>
    <h1>Deine Farbgebung</h1>
    <p>Aktuell gespeichert: <strong><?= e($aktuell) ?></strong></p>

    <form method="post" action="cookie.php">
        <p>
            <label>
                <input type="radio" name="farbe" value="hell"
                    <?= $aktuell === 'hell' ? 'checked' : '' ?>>
                hell
            </label>
            <label>
                <input type="radio" name="farbe" value="dunkel"
                    <?= $aktuell === 'dunkel' ? 'checked' : '' ?>>
                dunkel
            </label>
        </p>
        <p>
            <button type="submit">Merken</button>
        </p>
    </form>
</body>
</html>
