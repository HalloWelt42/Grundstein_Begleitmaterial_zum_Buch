<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Vorher)
 *
 * Der klassische Ausgangsschmerz: ein einziges Skript, das alles auf
 * einmal erledigt. Verbindungsaufbau, Eingabe lesen, Validierung,
 * Fachregel (keine doppelte Anmeldung), SQL und HTML-Ausgabe stehen
 * untrennbar durcheinander. Das läuft - und ist trotzdem kaum zu
 * ändern, kaum zu testen und nirgends wiederverwendbar.
 */

// Die Datenbank wird direkt hier aufgebaut - mitten in der Seite.
$pdo = new PDO('sqlite:' . __DIR__ . '/anmeldung.sqlite', null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$pdo->exec(
    'CREATE TABLE IF NOT EXISTS abonnent (
        id            INTEGER PRIMARY KEY,
        email         TEXT NOT NULL UNIQUE,
        angemeldet_am TEXT NOT NULL
    )'
);

// Eingabe roh aus dem Formular gegriffen.
$email = strtolower(trim((string) ($_POST['email'] ?? '')));

// Validierung, Fachregel, SQL und HTML - alles im selben Block vermengt.
if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    http_response_code(422);
    echo '<p>Bitte eine gültige E-Mail-Adresse angeben.</p>';

    return;
}

$suche = $pdo->prepare('SELECT COUNT(*) FROM abonnent WHERE email = :email');
$suche->execute(['email' => $email]);

if ((int) $suche->fetchColumn() > 0) {
    http_response_code(409);
    echo '<p>Diese Adresse ist bereits angemeldet.</p>';

    return;
}

$einfuegen = $pdo->prepare(
    'INSERT INTO abonnent (email, angemeldet_am) VALUES (:email, :am)'
);
$einfuegen->execute([
    'email' => $email,
    'am'    => date('Y-m-d H:i:s'),
]);

http_response_code(201);
echo '<p>Danke! ' . htmlspecialchars($email) . ' ist jetzt angemeldet.</p>';
