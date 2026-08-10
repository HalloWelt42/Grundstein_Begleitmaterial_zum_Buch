<?php

declare(strict_types=1);

/**
 * Zeigt SQL-Injection und ihre einzige richtige Abwehr, das Prepared
 * Statement. Der verwundbare Weg klebt eine Eingabe direkt in den
 * SQL-Text; der sichere Weg bindet sie als Parameter. Beide Wege laufen
 * gegen dieselbe In-Memory-Datenbank, damit der Unterschied sichtbar wird.
 *
 * Läuft ohne Server direkt im CLI:
 *   docker run --rm -v "$PWD":/app -w /app php:8.4-cli php kap39/injektion-falle.php
 */

$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

// Eine kleine Nutzertabelle mit einem Geheimnis pro Zeile.
$pdo->exec('CREATE TABLE konto (inhaber TEXT, geheim TEXT)');
$pdo->exec("INSERT INTO konto (inhaber, geheim) VALUES
    ('Anja', 'Tresorcode 1234'),
    ('Björn', 'Tresorcode 5678')");

// Statt eines echten Namens schickt der Angreifer SQL-Syntax mit.
$boeseEingabe = "Anja' OR '1'='1";

// --- FALSCH: Eingabe direkt in den SQL-Text geklebt -----------------

// Niemals so! Die Eingabe wird Teil des Befehls und kann seine
// Bedeutung verändern. Hier nur, um den Schaden vorzuführen.
$unsicher = "SELECT inhaber, geheim FROM konto WHERE inhaber = '{$boeseEingabe}'";
echo 'Zusammengebauter SQL:' . PHP_EOL;
echo '  ' . $unsicher . PHP_EOL;

$treffer = $pdo->query($unsicher)->fetchAll();
echo '  Preisgegebene Zeilen: ' . count($treffer) . PHP_EOL;
foreach ($treffer as $zeile) {
    echo '    ' . $zeile['inhaber'] . ': ' . $zeile['geheim'] . PHP_EOL;
}

// --- RICHTIG: dieselbe Eingabe als gebundener Parameter -------------

// Die Struktur der Abfrage steht fest, der Wert kommt getrennt nach.
// Er kann die Abfrage nicht mehr verändern - er ist immer nur Datum.
$sicher = $pdo->prepare('SELECT inhaber, geheim FROM konto WHERE inhaber = :name');
$sicher->execute(['name' => $boeseEingabe]);
$sicherTreffer = $sicher->fetchAll();

echo PHP_EOL . 'Sicher als Parameter gebunden:' . PHP_EOL;
echo '  Gefundene Zeilen: ' . count($sicherTreffer) . PHP_EOL;
