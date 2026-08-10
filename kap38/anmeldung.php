<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 38: Gemeinsame Bausteine für die Anmeldung.
 *
 * Diese Datei wird von login.php, geschuetzt.php und logout.php per
 * require eingebunden. Sie bündelt drei Dinge: die sichere Ausgabe,
 * den einheitlichen, sicheren Start der Session und die Prüfung der
 * Anmeldedaten gegen einen hinterlegten Benutzer.
 */

/**
 * Kurzform für die sichere HTML-Ausgabe (Schutz vor XSS).
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Startet die Session mit sicheren Voreinstellungen. Ein Aufruf steht
 * am Anfang jeder Seite, die auf $_SESSION zugreift. Ist die Session
 * schon aktiv, tut die Funktion nichts.
 */
function sichere_session_starten(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_start([
        'cookie_httponly' => true,                             // kein JS-Zugriff auf die Session-ID
        'cookie_secure'   => ($_SERVER['HTTPS'] ?? '') !== '', // nur über HTTPS mitschicken
        'cookie_samesite' => 'Lax',                            // schützt vor Fremd-POST
        'use_strict_mode' => true,                             // fremd erfundene IDs ablehnen
    ]);
}

/*
 * Ein winziger Benutzerspeicher für dieses Beispiel: ein Name und der
 * zugehörige bcrypt-Hash des Passworts. Ein Passwort wird NIE im
 * Klartext gespeichert; geprüft wird ausschließlich mit
 * password_verify() gegen den Hash. Wie man solche Hashes erzeugt,
 * Argon2id als moderne Alternative wählt und Benutzer in einer
 * Datenbank verwaltet, ist Thema des nächsten Kapitels.
 *
 * Das Klartext-Passwort zum Ausprobieren lautet: geheim123
 */
const BENUTZER_NAME = 'ada';
const BENUTZER_HASH = '$2y$12$9kXpT7DcB91D.pqMd4Xziephc3Ixtr1x21lXmEYQPrLzCsfjr90SW';

/**
 * Prüft Name und Passwort gegen den hinterlegten Benutzer. Gibt true
 * zurück, wenn beides passt, sonst false.
 */
function anmeldung_pruefen(string $name, string $passwort): bool
{
    // Den Namen in konstanter Zeit vergleichen - hash_equals verrät über
    // die Vergleichsdauer nicht, an welcher Stelle er abweicht.
    $nameStimmt = hash_equals(BENUTZER_NAME, $name);

    // password_verify läuft IMMER - auch bei falschem Namen. Der teure
    // bcrypt-Schritt dauert so bei jeder Anmeldung gleich lang und
    // verrät über die Antwortzeit nicht, ob der Name überhaupt
    // existiert. Sonst antwortete ein falscher Name messbar schneller
    // als ein richtiger Name mit falschem Passwort - ein Zeitkanal, der
    // gültige Namen aufdeckt (Enumeration).
    $passwortStimmt = password_verify($passwort, BENUTZER_HASH);

    // Beide Teilergebnisse liegen bereits als Boolean vor; ihre
    // Verknüpfung mit && läuft ohne weiteren messbaren Zeitunterschied.
    return $nameStimmt && $passwortStimmt;
}
