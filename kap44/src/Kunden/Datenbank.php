<?php

declare(strict_types=1);

namespace Grundstein\Kunden;

use PDO;

/**
 * Eine kleine Starthilfe für die Beispiel-API: Sie öffnet eine
 * SQLite-Datenbank als Datei, legt die Tabelle an, falls sie fehlt, und
 * füllt sie beim ersten Start mit ein paar Kunden. Für ein echtes Projekt
 * lägen Schema und Testdaten in Migrationen (Kapitel 33) - hier genügt
 * diese eine Datei, damit die curl-Aufrufe sofort etwas zu sehen bekommen.
 *
 * Wichtig: Es ist eine DATEI-Datenbank, keine im Speicher. Nur so überlebt
 * ein per POST angelegter Kunde bis zum nächsten GET, denn jeder Aufruf
 * startet ein frisches PHP-Skript.
 */
final class Datenbank
{
    public static function verbinden(string $pfad): PDO
    {
        $pdo = new PDO('sqlite:' . $pfad, options: [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS kunde (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                name        TEXT    NOT NULL,
                email       TEXT    NOT NULL,
                umsatz_cent INTEGER NOT NULL DEFAULT 0
            )'
        );

        self::seed($pdo);

        return $pdo;
    }

    /** Legt drei Kunden an, aber nur, wenn die Tabelle noch leer ist. */
    private static function seed(PDO $pdo): void
    {
        $anzahl = (int) $pdo->query('SELECT COUNT(*) FROM kunde')->fetchColumn();
        if ($anzahl > 0) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO kunde (name, email, umsatz_cent)
             VALUES (:name, :email, :umsatz)'
        );

        $start = [
            ['Ada Lovelace', 'ada@example.org', 12900],
            ['Grace Hopper', 'grace@example.org', 30050],
            ['Alan Turing', 'alan@example.org', 4500],
        ];

        foreach ($start as [$name, $email, $umsatz]) {
            $stmt->execute(['name' => $name, 'email' => $email, 'umsatz' => $umsatz]);
        }
    }
}
