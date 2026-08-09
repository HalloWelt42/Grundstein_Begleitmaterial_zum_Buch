<?php

declare(strict_types=1);

/*
 * Kapitel 30 - Datenbanken verstehen.
 *
 * Führt alle SQL-Beispiele des Kapitels real aus. Als Treiber dient
 * SQLite, das ohne Server auskommt und im Abbild php:8.4-cli bereits
 * enthalten ist (pdo_sqlite). Die Datenbank liegt im Arbeitsspeicher,
 * es entsteht keine Datei. Dieselben Abfragen laufen unverändert gegen
 * PostgreSQL oder MariaDB - dort ändert sich nur die DSN in PDO:
 *
 *   PostgreSQL: 'pgsql:host=db;port=5432;dbname=grundstein'
 *   MariaDB:    'mysql:host=db;port=3306;dbname=grundstein;charset=utf8mb4'
 *
 * PDO ist EIN einheitliches Programmierschnittstellen-Objekt für viele
 * Treiber. Kapitel 31 vertieft PDO; hier geht es nur um die Konzepte.
 */

// Verbindung zu einer Datenbank im Arbeitsspeicher aufbauen.
$pdo = new PDO('sqlite::memory:');
// Fehler sollen als Ausnahme fliegen, nicht still verpuffen.
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Fremdschlüssel in SQLite ausdrücklich aktivieren.
$pdo->exec('PRAGMA foreign_keys = ON');

/**
 * Gibt ein Abfrageergebnis als schlichte Texttabelle aus - eine eigene
 * kleine Hilfe, damit die Ausgabe im Buch wie eine Ergebnistabelle
 * aussieht. Zählt Spaltenbreiten mit mb_strlen, damit Umlaute korrekt
 * ausgerichtet werden.
 *
 * @param list<array<string, scalar|null>> $zeilen
 */
function zeigeTabelle(array $zeilen): void
{
    if ($zeilen === []) {
        echo '(keine Zeilen)' . PHP_EOL;

        return;
    }

    $spalten = array_keys($zeilen[0]);
    $breite = [];
    foreach ($spalten as $spalte) {
        $breite[$spalte] = mb_strlen((string) $spalte);
    }
    foreach ($zeilen as $zeile) {
        foreach ($spalten as $spalte) {
            $laenge = mb_strlen((string) $zeile[$spalte]);
            $breite[$spalte] = max($breite[$spalte], $laenge);
        }
    }

    // Füllt einen Wert rechts mit Leerzeichen auf die Spaltenbreite auf.
    $fuelle = static function (string $wert, int $ziel): string {
        return $wert . str_repeat(' ', $ziel - mb_strlen($wert));
    };

    $kopf = [];
    $linie = [];
    foreach ($spalten as $spalte) {
        $kopf[] = $fuelle((string) $spalte, $breite[$spalte]);
        $linie[] = str_repeat('-', $breite[$spalte]);
    }
    // rtrim entfernt die Füllzeichen der letzten Spalte am Zeilenende.
    echo rtrim(implode('  ', $kopf)) . PHP_EOL;
    echo rtrim(implode('  ', $linie)) . PHP_EOL;

    foreach ($zeilen as $zeile) {
        $zellen = [];
        foreach ($spalten as $spalte) {
            $zellen[] = $fuelle((string) $zeile[$spalte], $breite[$spalte]);
        }
        echo rtrim(implode('  ', $zellen)) . PHP_EOL;
    }
}

/**
 * Führt eine SELECT-Abfrage aus und gibt alle Zeilen als Liste zurück.
 *
 * @return list<array<string, scalar|null>>
 */
function frage(PDO $pdo, string $sql): array
{
    /** @var list<array<string, scalar|null>> $zeilen */
    $zeilen = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    return $zeilen;
}

// --- Schema anlegen (CREATE TABLE) -----------------------------------
$pdo->exec(<<<'SQL'
    CREATE TABLE kunde (
        id    INTEGER PRIMARY KEY,
        name  TEXT NOT NULL,
        stadt TEXT NOT NULL
    )
    SQL);

$pdo->exec(<<<'SQL'
    CREATE TABLE bestellung (
        id          INTEGER PRIMARY KEY,
        kunde_id    INTEGER NOT NULL,
        artikel     TEXT NOT NULL,
        betrag_cent INTEGER NOT NULL,
        bestellt_am TEXT NOT NULL,
        FOREIGN KEY (kunde_id) REFERENCES kunde (id)
    )
    SQL);

// --- Daten einfügen (INSERT) -----------------------------------------
$pdo->exec(<<<'SQL'
    INSERT INTO kunde (id, name, stadt) VALUES
        (1, 'Anna Krüger',  'Lüneburg'),
        (2, 'Björn Möller', 'Köln'),
        (3, 'Clara Groß',   'München')
    SQL);

$pdo->exec(<<<'SQL'
    INSERT INTO bestellung (id, kunde_id, artikel, betrag_cent, bestellt_am) VALUES
        (1, 1, 'Schraubenschlüssel', 1299, '2026-02-03'),
        (2, 1, 'Zollstock',           599, '2026-02-05'),
        (3, 2, 'Wasserwaage',        2450, '2026-02-04'),
        (4, 3, 'Akkuschrauber',      8990, '2026-02-06'),
        (5, 3, 'Bohrer-Set',         1590, '2026-02-07')
    SQL);

echo '== Alle Kunden (SELECT) ==' . PHP_EOL;
zeigeTabelle(frage($pdo, 'SELECT id, name, stadt FROM kunde'));

echo PHP_EOL . '== Teure Artikel, absteigend (WHERE, ORDER BY) ==' . PHP_EOL;
zeigeTabelle(frage($pdo, <<<'SQL'
    SELECT artikel, betrag_cent
    FROM bestellung
    WHERE betrag_cent >= 1500
    ORDER BY betrag_cent DESC
    SQL));

// --- Ändern (UPDATE) -------------------------------------------------
$pdo->exec('UPDATE bestellung SET betrag_cent = 1990 WHERE id = 2');
echo PHP_EOL . '== Nach dem UPDATE: Bestellung 2 ==' . PHP_EOL;
zeigeTabelle(frage($pdo, 'SELECT id, artikel, betrag_cent FROM bestellung WHERE id = 2'));

// --- Löschen (DELETE) ------------------------------------------------
$pdo->exec('DELETE FROM bestellung WHERE id = 5');
echo PHP_EOL . '== Nach dem DELETE: verbleibende Bestellungen ==' . PHP_EOL;
zeigeTabelle(frage($pdo, 'SELECT id, artikel FROM bestellung ORDER BY id'));

// --- Verbinden (JOIN) ------------------------------------------------
echo PHP_EOL . '== Kunde und Artikel zusammengeführt (JOIN) ==' . PHP_EOL;
zeigeTabelle(frage($pdo, <<<'SQL'
    SELECT kunde.name, bestellung.artikel, bestellung.betrag_cent
    FROM bestellung
    JOIN kunde ON kunde.id = bestellung.kunde_id
    ORDER BY kunde.name
    SQL));
