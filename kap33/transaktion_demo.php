<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 33: Migrationen
 *
 * Zeigt, warum jede Migration in einer Transaktion läuft. Eine
 * Migration mit zwei Schritten scheitert am zweiten. Die Transaktion
 * macht auch den ersten, schon gelungenen Schritt wieder rückgängig -
 * es bleibt keine halb angelegte Struktur zurück.
 */

$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

$pdo->beginTransaction();

try {
    // Erster Schritt: gelingt.
    $pdo->exec('CREATE TABLE lieferant (id INTEGER PRIMARY KEY, name TEXT NOT NULL)');

    // Zweiter Schritt: verweist auf eine Spalte, die es nicht gibt - Fehler.
    $pdo->exec('CREATE INDEX idx_plz ON lieferant (plz)');

    $pdo->commit();
    echo 'Migration vollständig angewendet.' . PHP_EOL;
} catch (Throwable $fehler) {
    // Der zweite Schritt scheitert - alles seit beginTransaction() fällt.
    $pdo->rollBack();
    echo 'Abgebrochen: ' . $fehler->getMessage() . PHP_EOL;
}

// Beweis: Obwohl der erste Schritt für sich gelungen war, existiert die
// Tabelle nicht - die Transaktion hat den halben Fortschritt verworfen.
$vorhanden = $pdo->query(
    "SELECT count(*) FROM sqlite_master WHERE type = 'table' AND name = 'lieferant'"
)->fetchColumn();

echo 'Tabelle lieferant vorhanden: ' . ($vorhanden ? 'ja' : 'nein') . PHP_EOL;
