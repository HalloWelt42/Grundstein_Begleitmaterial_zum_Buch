<?php

declare(strict_types=1);

/*
 * Ein kleiner, aber echter Migrations-Runner (Rückbezug Kapitel 31 und 33).
 * Im Deployment läuft genau dieser Schritt VOR dem Umschalten auf die neue
 * Version: Er wendet die SQL-Dateien aus migrations/ der Reihe nach an und
 * merkt sich in einer Tabelle schema_migrations, welche schon liefen. Ein
 * zweiter Lauf gegen dieselbe Datenbank tut deshalb nichts mehr - die
 * Migration ist gefahrlos wiederholbar.
 *
 * Die Zieldatenbank kommt aus der Umgebung (DB_DSN, Kapitel 57). Ohne
 * Angabe nimmt das Skript eine lokale SQLite-Datei, damit es überall sofort
 * lauffähig ist; im Betrieb zeigt DB_DSN auf die echte Datenbank.
 */

$dsn = getenv('DB_DSN') ?: 'sqlite:' . sys_get_temp_dir() . '/grundstein.sqlite';

$pdo = new PDO(
    $dsn,
    getenv('DB_USER') ?: null,
    getenv('DB_PASSWORD') ?: null,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

// Die Tabelle, die den Migrationsstand festhält - selbst rein additiv.
$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        version      TEXT PRIMARY KEY,
        angewandt_am TEXT NOT NULL
    )'
);

/** @var list<string> $erledigt */
$erledigt = $pdo->query('SELECT version FROM schema_migrations')
    ->fetchAll(PDO::FETCH_COLUMN);

$dateien = glob(dirname(__DIR__) . '/migrations/*.sql') ?: [];
sort($dateien); // Dateiname = Reihenfolge: nach Datum aufsteigend.

$angewandt = 0;
foreach ($dateien as $datei) {
    $version = basename($datei, '.sql');

    if (in_array($version, $erledigt, true)) {
        continue; // Diese Migration lief schon - überspringen.
    }

    $pdo->exec((string) file_get_contents($datei));

    $merken = $pdo->prepare(
        'INSERT INTO schema_migrations (version, angewandt_am)
         VALUES (:version, :zeit)'
    );
    $merken->execute(['version' => $version, 'zeit' => date('c')]);

    echo "Angewandt: {$version}" . PHP_EOL;
    ++$angewandt;
}

echo $angewandt === 0
    ? 'Keine ausstehenden Migrationen.' . PHP_EOL
    : "{$angewandt} Migration(en) angewandt." . PHP_EOL;
