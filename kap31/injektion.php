<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 31: Datenbankzugriff mit PDO
 *
 * Teil 3: Warum roh zusammengebauter SQL gefährlich ist. Diese Datei
 * zeigt eine SQL-Injection im direkten Vergleich - erst der falsche
 * Weg (Werte in den SQL-Text kleben), dann der richtige (Prepared
 * Statement). Der falsche Weg ist bewusst als abschreckendes Beispiel
 * gedacht, nicht zum Nachmachen.
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
    'CREATE TABLE konto (
        id      INTEGER PRIMARY KEY,
        inhaber TEXT    NOT NULL,
        geheim  TEXT    NOT NULL
    )'
);
$pdo->exec(
    "INSERT INTO konto (inhaber, geheim) VALUES
        ('Anja', 'Tresorcode 1234'),
        ('Björn', 'Tresorcode 5678')"
);

// So tut ein Angreifer, als suche er nur nach einem Namen. In Wahrheit
// schmuggelt die Eingabe SQL-Syntax ein.
$boeseEingabe = "Anja' OR '1'='1";

/*
 * FALSCH: Die Eingabe wird direkt in den SQL-Text geklebt. Aus dem
 * angehängten OR '1'='1 wird eine Bedingung, die immer wahr ist -
 * damit liefert die Abfrage ALLE Konten samt Geheimnissen.
 */
$unsicher = "SELECT inhaber, geheim FROM konto WHERE inhaber = '{$boeseEingabe}'";
echo 'Unsicher zusammengebauter SQL:' . PHP_EOL;
echo '  ' . $unsicher . PHP_EOL;

$treffer = $pdo->query($unsicher)->fetchAll();
echo '  Preisgegebene Zeilen: ' . count($treffer) . PHP_EOL;
foreach ($treffer as $zeile) {
    echo "    {$zeile['inhaber']}: {$zeile['geheim']}" . PHP_EOL;
}

/*
 * RICHTIG: Dieselbe Eingabe als gebundener Parameter. Die Datenbank
 * sucht jetzt wörtlich nach dem Namen "Anja' OR '1'='1" - den gibt es
 * nicht, also kommt kein Treffer zurück. Der Angriff verpufft.
 */
$sicher = $pdo->prepare(
    'SELECT inhaber, geheim FROM konto WHERE inhaber = :name'
);
$sicher->execute(['name' => $boeseEingabe]);
$sicherTreffer = $sicher->fetchAll();

echo 'Sicher als Parameter gebunden:' . PHP_EOL;
echo '  Gefundene Zeilen: ' . count($sicherTreffer) . PHP_EOL;
