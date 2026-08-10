<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang C: PDO-Referenz
 *
 * Beispiel 3: Massen-Insert. Viele Zeilen mit einer einzigen
 * vorbereiteten Anweisung einfuegen und alles in eine Transaktion
 * klammern. Das ist deutlich schneller als einzelne, jeweils sofort
 * festgeschriebene INSERTs, weil die Datenbank nur einmal committen
 * muss.
 *
 * Zeigt ausserdem rowCount() (betroffene Zeilen) und lastInsertId()
 * (der zuletzt vergebene Auto-Schluessel).
 *
 * Laeuft auf SQLite im Arbeitsspeicher. Alle Ausgaben stammen aus einem
 * echten Lauf mit PHP 8.4.
 */

$optionen = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO('sqlite::memory:', null, null, $optionen);

$pdo->exec(
    'CREATE TABLE messwert (
        id      INTEGER PRIMARY KEY,
        sensor  TEXT    NOT NULL,
        celsius REAL    NOT NULL
    )'
);

$einfuegen = $pdo->prepare(
    'INSERT INTO messwert (sensor, celsius) VALUES (:sensor, :celsius)'
);

// 1000 Messwerte in einer einzigen Transaktion einfuegen.
$pdo->beginTransaction();
try {
    for ($i = 1; $i <= 1000; $i++) {
        $einfuegen->execute([
            'sensor'  => 'sensor-' . ($i % 4),
            'celsius' => round(15 + sin($i / 10) * 8, 2),
        ]);
    }
    $pdo->commit();
} catch (Throwable $fehler) {
    $pdo->rollBack();
    throw $fehler;
}

// Gesamtzahl der Zeilen.
$gesamt = (int) $pdo->query('SELECT COUNT(*) FROM messwert')->fetchColumn();
echo "Eingefügte Messwerte: {$gesamt}" . PHP_EOL;

// lastInsertId(): der zuletzt vergebene Auto-Schluessel.
echo 'Letzte vergebene id: ' . $pdo->lastInsertId() . PHP_EOL;

// rowCount() nach einem UPDATE: wie viele Zeilen wurden geaendert?
$hoch = $pdo->prepare('UPDATE messwert SET celsius = celsius + 1 WHERE sensor = :s');
$hoch->execute(['s' => 'sensor-0']);
echo 'Von sensor-0 geändert (rowCount): ' . $hoch->rowCount() . PHP_EOL;

// Aggregat je Sensor - FETCH_KEY_PAIR liefert Spalte 1 als Schluessel,
// Spalte 2 als Wert.
$schnitt = $pdo->query(
    'SELECT sensor, ROUND(AVG(celsius), 2) FROM messwert GROUP BY sensor ORDER BY sensor'
)->fetchAll(PDO::FETCH_KEY_PAIR);

echo 'Mittelwert je Sensor (FETCH_KEY_PAIR):' . PHP_EOL;
foreach ($schnitt as $sensor => $mittel) {
    printf('  %s: %.2f °C' . PHP_EOL, $sensor, $mittel);
}
