<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 31: Datenbankzugriff mit PDO
 *
 * Teil 1: Die Verbindung aufbauen. PDO ist eine einheitliche
 * Objekt-Schnittstelle für viele Datenbanken. Was sich zwischen den
 * Systemen ändert, ist nur die DSN (der Verbindungsstring); der
 * restliche Code bleibt gleich.
 *
 * Wir nutzen hier SQLite im Arbeitsspeicher, damit das Beispiel ohne
 * Server läuft. Die DSN-Varianten für andere Systeme stehen als
 * Kommentar daneben.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/*
 * Die drei wichtigen Optionen für jede Verbindung:
 *
 * - ERRMODE_EXCEPTION: Fehler werfen eine PDOException, statt still
 *   false zurückzugeben. Das ist der einzige vernünftige Modus.
 * - DEFAULT_FETCH_MODE = FETCH_ASSOC: Ergebniszeilen kommen als
 *   assoziatives Array mit den Spaltennamen als Schlüssel.
 * - EMULATE_PREPARES = false: echte, vom Datenbanktreiber vorbereitete
 *   Anweisungen statt einer Nachbildung in PHP. Sicherer und ehrlicher
 *   bei den Datentypen.
 */
$optionen = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

/*
 * Die DSN entscheidet, welcher Treiber angesprochen wird. Nur diese
 * eine Zeile unterscheidet sich zwischen den Datenbanken:
 *
 *   SQLite (Datei):    'sqlite:/pfad/zur/datei.sqlite'
 *   SQLite (Speicher): 'sqlite::memory:'
 *   PostgreSQL:        'pgsql:host=db;port=5432;dbname=grundstein'
 *   MariaDB / MySQL:   'mysql:host=db;port=3306;dbname=grundstein;charset=utf8mb4'
 *
 * Bei PostgreSQL und MariaDB gibt man zusätzlich Benutzer und Passwort
 * an: new PDO($dsn, 'grundstein', 'geheim', $optionen). SQLite kennt
 * keine Anmeldung und braucht daher nur die DSN.
 */
$dsn = 'sqlite::memory:';
$pdo = new PDO($dsn, null, null, $optionen);

// Ein kleines Schema anlegen, damit wir etwas zum Abfragen haben.
$pdo->exec(
    'CREATE TABLE stadt (
        id        INTEGER PRIMARY KEY,
        name      TEXT    NOT NULL,
        einwohner INTEGER NOT NULL
    )'
);

// exec() gibt die Zahl der betroffenen Zeilen zurück - hier nützlich,
// um zu zeigen, dass wirklich drei Zeilen eingefügt wurden.
$anzahl = $pdo->exec(
    "INSERT INTO stadt (name, einwohner) VALUES
        ('Buxtehude', 40484),
        ('Lüneburg', 77007),
        ('Stade', 48124)"
);

echo "Eingefügte Zeilen: {$anzahl}" . PHP_EOL;

// Eine einfache Abfrage ohne Parameter: query() liefert ein
// PDOStatement, über das wir Zeile für Zeile laufen.
$ergebnis = $pdo->query('SELECT name, einwohner FROM stadt ORDER BY einwohner DESC');

echo 'Städte nach Größe:' . PHP_EOL;
foreach ($ergebnis as $zeile) {
    // Dank FETCH_ASSOC ist $zeile ein Array mit den Spaltennamen.
    printf('  %s: %d Einwohner' . PHP_EOL, $zeile['name'], $zeile['einwohner']);
}

// Welcher Treiber steckt hinter dieser Verbindung? Praktisch, wenn
// derselbe Code gegen mehrere Datenbanken laufen soll.
$treiber = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
echo "Aktiver Treiber: {$treiber}" . PHP_EOL;
