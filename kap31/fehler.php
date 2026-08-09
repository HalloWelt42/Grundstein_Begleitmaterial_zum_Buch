<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 31: Datenbankzugriff mit PDO
 *
 * Teil 5: Fehler behandeln. Weil wir ERRMODE_EXCEPTION gesetzt haben,
 * meldet PDO jeden Fehler als PDOException - dieselbe Mechanik wie in
 * Kapitel 26. So fängt man Datenbankfehler mit try/catch ab, statt
 * nach jedem Aufruf einen Rückgabewert zu prüfen.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

$optionen = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO('sqlite::memory:', null, null, $optionen);

$pdo->exec(
    'CREATE TABLE nutzer (
        id    INTEGER PRIMARY KEY,
        email TEXT    NOT NULL UNIQUE
    )'
);

/*
 * Erster Fehler: eine E-Mail zweimal einfügen, obwohl die Spalte
 * UNIQUE ist. Der zweite Versuch verletzt die Bedingung und wirft eine
 * PDOException.
 */
$einfuegen = $pdo->prepare('INSERT INTO nutzer (email) VALUES (:email)');

foreach (['anja@example.org', 'anja@example.org'] as $email) {
    try {
        $einfuegen->execute(['email' => $email]);
        echo "Angelegt: {$email}" . PHP_EOL;
    } catch (PDOException $fehler) {
        // getCode() liefert den SQLSTATE-Code (hier 23000 für eine
        // verletzte Integritätsbedingung).
        echo 'Abgelehnt (' . $fehler->getCode() . '): ' . $email . PHP_EOL;
    }
}

/*
 * Zweiter Fehler: eine Abfrage auf eine Spalte, die es nicht gibt.
 * Auch ein solcher SQL-Fehler kommt als PDOException an.
 */
try {
    $pdo->query('SELECT vorname FROM nutzer');
} catch (PDOException $fehler) {
    echo 'SQL-Fehler abgefangen: ' . $fehler->getMessage() . PHP_EOL;
}
