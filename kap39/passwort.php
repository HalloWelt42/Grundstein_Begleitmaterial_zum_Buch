<?php

declare(strict_types=1);

/**
 * Zeigt den richtigen Umgang mit Passwörtern: hashen mit
 * password_hash(), prüfen mit password_verify() und bei Bedarf
 * neu hashen mit password_needs_rehash(). Ein Passwort wird niemals
 * im Klartext gespeichert und niemals selbst mit md5/sha1 gehasht.
 *
 * Läuft ohne Server direkt im CLI:
 *   docker run --rm -v "$PWD":/app -w /app php:8.4-cli php kap39/passwort.php
 */

// Das Passwort, das der Nutzer bei der Registrierung eingibt.
$passwort = 'korrektes-pferd-batterie-heftklammer';

// --- 1. Hashen mit dem sicheren Standardverfahren -------------------

// PASSWORD_DEFAULT wählt das derzeit empfohlene Verfahren (bcrypt).
// Der Hash enthält bereits das Verfahren, den Kostenfaktor und einen
// zufälligen Salt - genau dieser String kommt in die Datenbank.
$hash = password_hash($passwort, PASSWORD_DEFAULT);

echo 'Gespeicherter Hash:' . PHP_EOL;
echo '  ' . $hash . PHP_EOL;
echo '  Länge: ' . strlen($hash) . ' Zeichen' . PHP_EOL . PHP_EOL;

// Derselbe Klartext liefert bei jedem Aufruf einen anderen Hash, weil
// jedes Mal ein neuer Salt erzeugt wird. Das ist gewollt und richtig.
$hashZwei = password_hash($passwort, PASSWORD_DEFAULT);
echo 'Zweiter Hash desselben Passworts ist:' . PHP_EOL;
echo '  ' . ($hash === $hashZwei ? 'gleich' : 'verschieden') . PHP_EOL . PHP_EOL;

// --- 2. Prüfen mit password_verify() --------------------------------

// Beim Anmelden vergleicht password_verify() den eingegebenen Klartext
// mit dem gespeicherten Hash. Man entschlüsselt den Hash nicht - das
// geht gar nicht -, sondern lässt PHP den Vergleich sicher erledigen.
$eingabeRichtig = 'korrektes-pferd-batterie-heftklammer';
$eingabeFalsch  = 'geheim123';

echo 'Anmeldung mit richtigem Passwort: '
    . (password_verify($eingabeRichtig, $hash) ? 'akzeptiert' : 'abgelehnt') . PHP_EOL;
echo 'Anmeldung mit falschem Passwort:  '
    . (password_verify($eingabeFalsch, $hash) ? 'akzeptiert' : 'abgelehnt') . PHP_EOL . PHP_EOL;

// --- 3. Den Kostenfaktor bewusst wählen -----------------------------

// Der Kostenfaktor bestimmt, wie rechenintensiv das Hashen ist. Ein
// höherer Wert macht Angriffe teurer, kostet aber auch beim Anmelden
// Zeit. 12 ist ein vernünftiger Ausgangswert für bcrypt.
$teurerHash = password_hash($passwort, PASSWORD_BCRYPT, ['cost' => 12]);
echo 'Hash mit Kostenfaktor 12:' . PHP_EOL;
echo '  ' . $teurerHash . PHP_EOL . PHP_EOL;

// --- 4. Alte Hashes erkennen und erneuern ---------------------------

// Wurde ein Hash mit einem schwächeren Kostenfaktor erzeugt, meldet
// password_needs_rehash() das. Man erneuert ihn dann beim nächsten
// erfolgreichen Login - da hat man den Klartext ohnehin gerade zur Hand.
$alterHash = password_hash($passwort, PASSWORD_BCRYPT, ['cost' => 10]);

$brauchtErneuerung = password_needs_rehash($alterHash, PASSWORD_BCRYPT, ['cost' => 12]);
echo 'Alter Hash (Kosten 10) braucht Erneuerung auf Kosten 12: '
    . ($brauchtErneuerung ? 'ja' : 'nein') . PHP_EOL;

if ($brauchtErneuerung && password_verify($passwort, $alterHash)) {
    // Erst prüfen, dann neu hashen und den neuen Hash speichern.
    $neuerHash = password_hash($passwort, PASSWORD_BCRYPT, ['cost' => 12]);
    echo 'Erneuert. Neuer Hash würde jetzt gespeichert.' . PHP_EOL;
}

// --- 5. Ausblick: Argon2id ------------------------------------------

// Wurde PHP mit Argon2-Unterstützung gebaut (libargon2, Compile-Flag
// --with-password-argon2; im offiziellen PHP-Abbild ist das der Fall),
// lässt sich auch das moderne Verfahren Argon2id nutzen. Der übrige
// Code bleibt gleich - password_verify() erkennt das Verfahren am Hash.
if (defined('PASSWORD_ARGON2ID')) {
    $argonHash = password_hash($passwort, PASSWORD_ARGON2ID);
    echo PHP_EOL . 'Argon2id ist verfügbar. Beispiel-Hash:' . PHP_EOL;
    echo '  ' . $argonHash . PHP_EOL;
} else {
    echo PHP_EOL . 'Argon2id ist in diesem Build nicht verfügbar.' . PHP_EOL;
}
