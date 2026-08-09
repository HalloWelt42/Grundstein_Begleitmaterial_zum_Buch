<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 33: Migrationen
 *
 * Zeigt nach einem Lauf von migrate.php, was tatsächlich in der
 * Datenbank steht: die Buchführung in schema_migration und die
 * gewachsenen Spalten der Tabelle kunde.
 */

$pdo = new PDO('sqlite:' . __DIR__ . '/datenbank.sqlite', null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

echo 'Angewendete Migrationen:' . PHP_EOL;
foreach ($pdo->query('SELECT version FROM schema_migration ORDER BY version') as $zeile) {
    echo '  ' . $zeile['version'] . PHP_EOL;
}

echo 'Spalten der Tabelle kunde:' . PHP_EOL;
foreach ($pdo->query('PRAGMA table_info(kunde)') as $spalte) {
    echo '  ' . $spalte['name'] . ' (' . $spalte['type'] . ')' . PHP_EOL;
}
