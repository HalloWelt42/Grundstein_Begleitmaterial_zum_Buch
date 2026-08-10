<?php

declare(strict_types=1);

/**
 * Schützt ein Formular gegen Cross-Site-Request-Forgery (CSRF) mit dem
 * Token-Muster: Der Server legt ein zufälliges Token in der Session ab
 * und schickt es als verstecktes Feld ins Formular. Beim Absenden muss
 * das mitgeschickte Token zum gespeicherten passen, sonst wird die
 * Anfrage abgelehnt. Verglichen wird mit hash_equals().
 *
 * Prüfen mit dem eingebauten Webserver und curl (Cookie-Jar):
 *   docker run --rm -p 127.0.0.1:8080:8080 -v "$PWD":/app -w /app \
 *       php:8.4-cli php -S 0.0.0.0:8080
 */

// Sichere Cookie-Flags, bevor die Session startet. httponly hält das
// Cookie vor JavaScript verborgen, samesite dämmt CSRF zusätzlich ein.
// secure bleibt hier aus, weil der Entwicklungsserver ohne HTTPS läuft;
// in Produktion (HTTPS) gehört es auf true.
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => false,
]);
session_start();

/**
 * Kurzform für die sichere HTML-Ausgabe (siehe Kapitel über Formulare).
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Liefert das CSRF-Token der Session und erzeugt es beim ersten Aufruf.
 * random_bytes() ist kryptografisch sicher; bin2hex() macht daraus einen
 * druckbaren String, der sich gefahrlos ins HTML einbetten lässt.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Prüft ein aus dem Formular kommendes Token gegen das der Session.
 * hash_equals() vergleicht in konstanter Zeit und verrät damit über die
 * Antwortdauer nichts über die Anzahl übereinstimmender Zeichen.
 */
function csrfPruefen(?string $ausFormular): bool
{
    $erwartet = $_SESSION['csrf_token'] ?? '';

    return $ausFormular !== null
        && $erwartet !== ''
        && hash_equals($erwartet, $ausFormular);
}

// --- Ablaufsteuerung ------------------------------------------------

$meldung = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfPruefen($_POST['csrf_token'] ?? null)) {
        // Kein oder falsches Token: Anfrage ablehnen, nichts verändern.
        http_response_code(400);
        $meldung = 'Abgelehnt: ungültiges oder fehlendes CSRF-Token.';
    } else {
        // Token in Ordnung - hier würde die eigentliche Aktion laufen.
        $name = trim((string) ($_POST['name'] ?? ''));
        $meldung = 'Angenommen. Danke, ' . e($name !== '' ? $name : 'Unbekannt') . '!';
    }
}

$token = csrfToken();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>CSRF-geschütztes Formular</title>
</head>
<body>
    <h1>Profil ändern</h1>
<?php if ($meldung !== ''): ?>
    <p><?= e($meldung) ?></p>
<?php endif; ?>
    <form method="post" action="csrf.php">
        <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
        <p>
            <label>Anzeigename:<br>
                <input type="text" name="name" value="">
            </label>
        </p>
        <p>
            <button type="submit">Speichern</button>
        </p>
    </form>
</body>
</html>
