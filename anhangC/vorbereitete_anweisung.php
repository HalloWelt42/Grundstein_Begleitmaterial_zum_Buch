<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang C: PDO-Referenz
 *
 * Beispiel 1: Vorbereitete Anweisungen (Prepared Statements).
 *
 * Zeigt die beiden Platzhalter-Arten (benannt :name und positionell ?)
 * und die drei wichtigsten Abrufwege: fetch() fuer eine Zeile,
 * fetchAll() fuer alle Zeilen, fetchColumn() fuer einen einzelnen Wert.
 *
 * Laeuft ohne Server auf SQLite im Arbeitsspeicher. Alle Ausgaben
 * stammen aus einem echten Lauf mit PHP 8.4.
 */

$optionen = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO('sqlite::memory:', null, null, $optionen);

$pdo->exec(
    'CREATE TABLE person (
        id      INTEGER PRIMARY KEY,
        name    TEXT    NOT NULL,
        stadt   TEXT    NOT NULL,
        alter_j INTEGER NOT NULL
    )'
);

// Einmal vorbereiten, mehrfach ausfuehren. Die Array-Schluessel muessen
// zu den benannten Platzhaltern passen.
$einfuegen = $pdo->prepare(
    'INSERT INTO person (name, stadt, alter_j)
     VALUES (:name, :stadt, :alter_j)'
);

$leute = [
    ['name' => 'Anja',  'stadt' => 'Buxtehude', 'alter_j' => 34],
    ['name' => 'Björn', 'stadt' => 'Lüneburg',  'alter_j' => 41],
    ['name' => 'Cem',   'stadt' => 'Buxtehude', 'alter_j' => 29],
    ['name' => 'Dörte', 'stadt' => 'Stade',     'alter_j' => 52],
];

foreach ($leute as $person) {
    $einfuegen->execute($person);
}
echo 'Eingefügt: ' . count($leute) . ' Personen.' . PHP_EOL;

// (1) Benannter Platzhalter + fetch(): Zeile fuer Zeile lesen.
$suche = $pdo->prepare(
    'SELECT name, alter_j FROM person WHERE stadt = :stadt ORDER BY name'
);
$suche->execute(['stadt' => 'Buxtehude']);

echo 'Aus Buxtehude (fetch):' . PHP_EOL;
while ($zeile = $suche->fetch()) {
    printf('  %s (%d Jahre)' . PHP_EOL, $zeile['name'], $zeile['alter_j']);
}

// (2) Positioneller Platzhalter + fetchAll(): alle Treffer auf einmal.
$abZahl = $pdo->prepare(
    'SELECT name, stadt FROM person WHERE alter_j >= ? ORDER BY alter_j DESC'
);
$abZahl->execute([40]);
$alle = $abZahl->fetchAll();

echo 'Ab 40 Jahren (fetchAll):' . PHP_EOL;
foreach ($alle as $zeile) {
    printf('  %s aus %s' . PHP_EOL, $zeile['name'], $zeile['stadt']);
}

// (3) fetchColumn(): einen einzelnen Skalarwert holen.
$zaehlen = $pdo->prepare('SELECT COUNT(*) FROM person WHERE stadt = ?');
$zaehlen->execute(['Buxtehude']);
$anzahl = (int) $zaehlen->fetchColumn();
echo "Personen in Buxtehude (fetchColumn): {$anzahl}" . PHP_EOL;
