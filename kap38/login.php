<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 38: Anmeldung.
 *
 * Bei GET zeigt die Seite das Anmeldeformular. Bei POST prüft sie die
 * Daten. Stimmen sie, wird die Session-ID erneuert (Schutz vor Session
 * Fixation), der Benutzer in der Session vermerkt und per Umleitung auf
 * die geschützte Seite weitergeleitet.
 */

require __DIR__ . '/anmeldung.php';

sichere_session_starten();

$fehler = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim((string) ($_POST['name'] ?? ''));
    $passwort = (string) ($_POST['passwort'] ?? '');

    if (anmeldung_pruefen($name, $passwort)) {
        // Kernschutz gegen Session Fixation: nach erfolgreicher
        // Anmeldung eine frische Session-ID vergeben. Das Argument true
        // löscht die alte Session-Datei gleich mit.
        session_regenerate_id(true);

        // Ab jetzt gilt die Sitzung als angemeldet.
        $_SESSION['benutzer'] = $name;

        // Post/Redirect/Get: auf die geschützte Seite umleiten.
        header('Location: geschuetzt.php', true, 303);
        exit;
    }

    // Bewusst neutral: nicht verraten, ob Name oder Passwort falsch war.
    $fehler = 'Name oder Passwort ist falsch.';
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Anmeldung</title>
</head>
<body>
    <h1>Anmeldung</h1>

<?php if ($fehler !== ''): ?>
    <p class="fehler"><?= e($fehler) ?></p>
<?php endif; ?>

    <form method="post" action="login.php">
        <p>
            <label>Name:<br>
                <input type="text" name="name" value="">
            </label>
        </p>
        <p>
            <label>Passwort:<br>
                <input type="password" name="passwort" value="">
            </label>
        </p>
        <p>
            <button type="submit">Anmelden</button>
        </p>
    </form>
</body>
</html>
