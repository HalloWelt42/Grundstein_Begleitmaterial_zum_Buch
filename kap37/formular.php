<?php

declare(strict_types=1);

require __DIR__ . '/validierung.php';

/**
 * Kurzform für die sichere HTML-Ausgabe. Jeder Wert, der aus einer
 * Nutzereingabe stammt, läuft durch diese Funktion, bevor er ins HTML
 * kommt - sonst wäre die Seite für Cross-Site-Scripting (XSS) offen.
 * ENT_QUOTES escaped auch einfache und doppelte Anführungszeichen,
 * damit Werte auch innerhalb von value="..." sicher sind.
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Nimmt die Rohdaten aus $_POST entgegen und liefert getrimmte
 * Strings mit festen Schlüsseln. Die Superglobale wird hier gekapselt
 * und nicht roh durch das ganze Programm gereicht - ein Vorgriff auf
 * die Request-Objekte, die wir später als Standard kennenlernen.
 *
 * @param array<string, mixed> $eingabe
 * @return array{name: string, email: string, alter: string, nachricht: string}
 */
function leseEingabe(array $eingabe): array
{
    return [
        'name'      => trim((string) ($eingabe['name'] ?? '')),
        'email'     => trim((string) ($eingabe['email'] ?? '')),
        'alter'     => trim((string) ($eingabe['alter'] ?? '')),
        'nachricht' => trim((string) ($eingabe['nachricht'] ?? '')),
    ];
}

/**
 * Prüft die eingelesenen Daten und gibt den fertig befüllten
 * Validator zurück.
 *
 * @param array{name: string, email: string, alter: string, nachricht: string} $daten
 */
function pruefe(array $daten): Validator
{
    $pruefer = new Validator();

    $pruefer
        ->pflicht('name', $daten['name'], 'Bitte gib deinen Namen an.')
        ->pflicht('email', $daten['email'], 'Bitte gib eine E-Mail-Adresse an.')
        ->email('email', $daten['email'], 'Das ist keine gültige E-Mail-Adresse.')
        ->pflicht('nachricht', $daten['nachricht'], 'Bitte schreib eine Nachricht.')
        ->minLaenge('nachricht', $daten['nachricht'], 10, 'Die Nachricht ist zu kurz (mindestens 10 Zeichen).');

    // Das Alter ist freiwillig - nur prüfen, wenn etwas dasteht.
    if ($daten['alter'] !== '') {
        $pruefer->ganzzahlBereich('alter', $daten['alter'], 16, 120, 'Bitte gib ein Alter zwischen 16 und 120 an.');
    }

    return $pruefer;
}

// --- Ablaufsteuerung ------------------------------------------------

// Vorbelegung für die erste Anzeige (GET): alle Felder leer.
$daten = ['name' => '', 'email' => '', 'alter' => '', 'nachricht' => ''];
$pruefer = new Validator();
$abgeschickt = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $abgeschickt = true;
    $daten = leseEingabe($_POST);
    $pruefer = pruefe($daten);

    if ($pruefer->istGueltig()) {
        // Hier würden die Daten dauerhaft gespeichert oder verschickt.
        // Danach folgt der Kern des Musters Post/Redirect/Get: eine
        // Umleitung mit Status 303 auf eine reine GET-Seite. So führt
        // ein Neuladen der Danke-Seite nicht zu einem zweiten Absenden.
        header('Location: danke.php', true, 303);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Kontakt</title>
</head>
<body>
    <h1>Schreib uns</h1>

<?php if ($abgeschickt && !$pruefer->istGueltig()): ?>
    <p>Bitte korrigiere die folgenden Angaben:</p>
    <ul>
<?php foreach ($pruefer->alleMeldungen() as $meldung): ?>
        <li><?= e($meldung) ?></li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>

    <form method="post" action="formular.php">
        <p>
            <label>Name:<br>
                <input type="text" name="name" value="<?= e($daten['name']) ?>">
            </label>
<?php if ($pruefer->fehlerFuer('name') !== ''): ?>
            <span class="fehler"><?= e($pruefer->fehlerFuer('name')) ?></span>
<?php endif; ?>
        </p>
        <p>
            <label>E-Mail:<br>
                <input type="email" name="email" value="<?= e($daten['email']) ?>">
            </label>
<?php if ($pruefer->fehlerFuer('email') !== ''): ?>
            <span class="fehler"><?= e($pruefer->fehlerFuer('email')) ?></span>
<?php endif; ?>
        </p>
        <p>
            <label>Alter (freiwillig):<br>
                <input type="text" name="alter" value="<?= e($daten['alter']) ?>">
            </label>
<?php if ($pruefer->fehlerFuer('alter') !== ''): ?>
            <span class="fehler"><?= e($pruefer->fehlerFuer('alter')) ?></span>
<?php endif; ?>
        </p>
        <p>
            <label>Nachricht:<br>
                <textarea name="nachricht"><?= e($daten['nachricht']) ?></textarea>
            </label>
<?php if ($pruefer->fehlerFuer('nachricht') !== ''): ?>
            <span class="fehler"><?= e($pruefer->fehlerFuer('nachricht')) ?></span>
<?php endif; ?>
        </p>
        <p>
            <button type="submit">Absenden</button>
        </p>
    </form>
</body>
</html>
