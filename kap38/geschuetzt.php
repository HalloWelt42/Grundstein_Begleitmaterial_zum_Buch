<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 38: Die geschützte Seite.
 *
 * Sie ist nur für angemeldete Besucher sichtbar. Als Allererstes prüft
 * eine Wache, ob in der Session ein Benutzer vermerkt ist. Fehlt er,
 * gibt es keinen Zutritt, und die Seite leitet zurück zur Anmeldung -
 * bevor eine einzige Zeile Inhalt entsteht.
 */

require __DIR__ . '/anmeldung.php';

sichere_session_starten();

// Die Wache: ohne angemeldeten Benutzer kein Zutritt.
if (!isset($_SESSION['benutzer'])) {
    header('Location: login.php', true, 303);
    exit;
}

$benutzer = (string) $_SESSION['benutzer'];

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Geschützter Bereich</title>
</head>
<body>
    <h1>Hallo, <?= e($benutzer) ?>!</h1>
    <p>Diese Seite sehen nur angemeldete Besucher.</p>

    <form method="post" action="logout.php">
        <button type="submit">Abmelden</button>
    </form>
</body>
</html>
