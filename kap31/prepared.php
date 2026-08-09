<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 31: Datenbankzugriff mit PDO
 *
 * Teil 2: Prepared Statements - das Herz von PDO. Werte werden nie in
 * den SQL-Text eingebaut, sondern getrennt als Parameter übergeben.
 * Die Datenbank kennt so die Struktur der Abfrage vorher und behandelt
 * die Werte immer nur als Daten, nie als Befehl. Genau das schließt
 * SQL-Injection aus.
 *
 * Zwei Formen: benannte Platzhalter (:name) und positionelle (?).
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
    'CREATE TABLE person (
        id      INTEGER PRIMARY KEY,
        name    TEXT    NOT NULL,
        stadt   TEXT    NOT NULL,
        alter_j INTEGER NOT NULL
    )'
);

/*
 * Einfügen mit BENANNTEN Platzhaltern. Die Anweisung wird einmal
 * vorbereitet und dann mehrfach mit verschiedenen Werten ausgeführt -
 * effizient und sauber.
 */
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
    // Das Array wird direkt an execute() übergeben; die Schlüssel
    // müssen zu den Platzhaltern passen.
    $einfuegen->execute($person);
}

echo 'Eingefügt: ' . count($leute) . ' Personen.' . PHP_EOL;

/*
 * Abfrage mit einem benannten Parameter. Der Wert für :stadt kommt vom
 * Nutzer und wird sicher gebunden - selbst bösartige Eingaben können
 * die Abfrage nicht mehr verändern.
 */
$gesucht = 'Buxtehude';

$suche = $pdo->prepare(
    'SELECT name, alter_j FROM person WHERE stadt = :stadt ORDER BY name'
);
$suche->execute(['stadt' => $gesucht]);

echo "Personen aus {$gesucht}:" . PHP_EOL;
// fetch() holt eine Zeile nach der anderen, bis es nichts mehr gibt.
while ($zeile = $suche->fetch()) {
    printf('  %s (%d Jahre)' . PHP_EOL, $zeile['name'], $zeile['alter_j']);
}

/*
 * Dieselbe Abfrage mit einem POSITIONELLEN Platzhalter (?). Die Werte
 * werden als Liste in der richtigen Reihenfolge übergeben. fetchAll()
 * liefert alle Zeilen auf einmal als Array.
 */
$mindestalter = 30;

$abZahl = $pdo->prepare(
    'SELECT name, stadt, alter_j FROM person WHERE alter_j >= ? ORDER BY alter_j'
);
$abZahl->execute([$mindestalter]);
$alle = $abZahl->fetchAll();

echo "Ab {$mindestalter} Jahren (" . count($alle) . ' Treffer):' . PHP_EOL;
foreach ($alle as $zeile) {
    printf('  %s aus %s, %d Jahre' . PHP_EOL, $zeile['name'], $zeile['stadt'], $zeile['alter_j']);
}
