<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 33: Migrationen
 *
 * Ein winziger, selbst gebauter Migrations-Runner zum Verstehen. Er
 * liest .sql- und .php-Migrationen aus einem Ordner, wendet die noch
 * nicht angewendeten der Reihe nach in einer Transaktion an und trägt
 * jede erfolgreiche in eine Migrations-Tabelle ein.
 *
 * Aufruf:
 *   php migrate.php           - alle offenen Migrationen anwenden (up)
 *   php migrate.php status    - anzeigen, was gelaufen ist und was fehlt
 *   php migrate.php down      - die zuletzt angewendete zurücknehmen
 */

use App\Migration\Migration;

// Der Vertrag für PHP-Migrationen muss bekannt sein, bevor eine
// Migrationsdatei geladen wird, die ihn erfüllt.
require_once __DIR__ . '/src/Migration.php';

const DB_DATEI       = __DIR__ . '/datenbank.sqlite';
const MIGRATIONS_DIR = __DIR__ . '/migrationen';

/**
 * Baut die Datenbankverbindung mit den empfohlenen Optionen auf.
 */
function verbinden(): PDO
{
    return new PDO('sqlite:' . DB_DATEI, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

/**
 * Stellt sicher, dass die Buchführungs-Tabelle existiert. Sie hält
 * fest, welche Migration wann gelaufen ist.
 */
function tabelleVorbereiten(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS schema_migration (
            version        TEXT PRIMARY KEY,
            ausgefuehrt_am TEXT NOT NULL
        )'
    );
}

/**
 * Liefert die schon angewendeten Versionen als schnelle Nachschlage-Menge.
 *
 * @return array<string, true>
 */
function angewendeteVersionen(PDO $pdo): array
{
    $menge = [];
    foreach ($pdo->query('SELECT version FROM schema_migration') as $zeile) {
        $menge[$zeile['version']] = true;
    }

    return $menge;
}

/**
 * Liest alle Migrationsdateien, aufsteigend nach Namen sortiert. Das
 * Namensschema (001_, 002_, ...) legt damit die Reihenfolge fest.
 *
 * @return list<string> Vollständige Pfade der Migrationsdateien.
 */
function migrationsdateien(string $ordner): array
{
    $dateien = glob($ordner . '/*.{sql,php}', GLOB_BRACE);
    sort($dateien);

    return $dateien;
}

/**
 * Wendet eine einzelne Migration vorwärts an - je nach Dateiendung als
 * SQL-Text oder als PHP-Objekt mit up()-Methode.
 */
function migrationHoch(PDO $pdo, string $datei): void
{
    if (str_ends_with($datei, '.sql')) {
        $pdo->exec((string) file_get_contents($datei));

        return;
    }

    // .php: die Datei gibt ein Migration-Objekt zurück.
    $migration = require $datei;

    if (!$migration instanceof Migration) {
        throw new RuntimeException(basename($datei) . ' gibt keine Migration zurück.');
    }

    $migration->up($pdo);
}

/**
 * Wendet alle noch offenen Migrationen der Reihe nach an. Jede läuft in
 * ihrer eigenen Transaktion: Klappt sie ganz, wird sie eingetragen;
 * scheitert sie, macht rollBack() den halben Schritt rückgängig.
 */
function hoch(PDO $pdo): void
{
    tabelleVorbereiten($pdo);
    $schon = angewendeteVersionen($pdo);

    $eintragen = $pdo->prepare(
        'INSERT INTO schema_migration (version, ausgefuehrt_am) VALUES (:version, :zeit)'
    );

    $angewendet = 0;
    foreach (migrationsdateien(MIGRATIONS_DIR) as $datei) {
        $version = basename($datei);

        // Schon gelaufen? Dann überspringen - das macht den Lauf idempotent.
        if (isset($schon[$version])) {
            continue;
        }

        $pdo->beginTransaction();
        try {
            migrationHoch($pdo, $datei);
            $eintragen->execute(['version' => $version, 'zeit' => date('Y-m-d H:i:s')]);
            $pdo->commit();
        } catch (Throwable $fehler) {
            // Ein misslungener Schritt darf keine halben Spuren hinterlassen.
            $pdo->rollBack();
            echo 'Fehlgeschlagen bei ' . $version . ': ' . $fehler->getMessage() . PHP_EOL;
            throw $fehler;
        }

        echo 'Angewendet: ' . $version . PHP_EOL;
        $angewendet++;
    }

    echo $angewendet === 0
        ? 'Nichts zu tun - das Schema ist aktuell.' . PHP_EOL
        : $angewendet . ' Migration(en) angewendet.' . PHP_EOL;
}

/**
 * Nimmt die zuletzt angewendete Migration zurück. Das gelingt nur bei
 * PHP-Migrationen, die eine down()-Methode mitbringen.
 */
function runter(PDO $pdo): void
{
    tabelleVorbereiten($pdo);

    $letzte = $pdo->query(
        'SELECT version FROM schema_migration ORDER BY version DESC LIMIT 1'
    )->fetchColumn();

    if ($letzte === false) {
        echo 'Keine Migration angewendet - nichts zurückzunehmen.' . PHP_EOL;

        return;
    }

    // Eine reine SQL-Migration kennt keinen Rückweg.
    if (str_ends_with((string) $letzte, '.sql')) {
        echo 'Die SQL-Migration ' . $letzte . ' kennt keinen Rückweg.' . PHP_EOL;

        return;
    }

    $migration = require MIGRATIONS_DIR . '/' . $letzte;

    // Derselbe Schutz wie beim Vorwärtsweg: erst prüfen, dann down() aufrufen.
    if (!$migration instanceof Migration) {
        throw new RuntimeException(basename($letzte) . ' gibt keine Migration zurück.');
    }

    $pdo->beginTransaction();
    try {
        $migration->down($pdo);
        $pdo->prepare('DELETE FROM schema_migration WHERE version = :version')
            ->execute(['version' => $letzte]);
        $pdo->commit();
    } catch (Throwable $fehler) {
        $pdo->rollBack();
        throw $fehler;
    }

    echo 'Zurückgenommen: ' . $letzte . PHP_EOL;
}

/**
 * Zeigt für jede Migrationsdatei, ob sie schon gelaufen ist.
 */
function status(PDO $pdo): void
{
    tabelleVorbereiten($pdo);
    $schon = angewendeteVersionen($pdo);

    foreach (migrationsdateien(MIGRATIONS_DIR) as $datei) {
        $version = basename($datei);
        $zeichen = isset($schon[$version]) ? '[x]' : '[ ]';
        echo $zeichen . ' ' . $version . PHP_EOL;
    }
}

// --- Einstieg: das erste Argument wählt den Befehl. ------------------
$befehl = $argv[1] ?? 'up';
$pdo = verbinden();

match ($befehl) {
    'up'     => hoch($pdo),
    'down'   => runter($pdo),
    'status' => status($pdo),
    default  => print('Unbekannter Befehl: ' . $befehl . PHP_EOL),
};
