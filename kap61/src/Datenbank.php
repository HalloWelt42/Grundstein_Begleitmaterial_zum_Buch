<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Kleiner Fabrik-Helfer für die SQLite-Verbindung der Warteschlange.
 *
 * Die Verbindung wird mit denselben drei Optionen wie in Kapitel 31
 * aufgebaut (Ausnahmen, assoziative Zeilen, echte Prepared Statements).
 * Zusätzlich setzt sie ein Wartelimit: Konkurrieren mehrere Worker um
 * dieselbe Datei, wartet PDO bei einer gehaltenen Sperre ein paar
 * Sekunden, statt sofort mit "database is locked" abzubrechen.
 */
final class Datenbank
{
    /**
     * Öffnet eine SQLite-Verbindung für die Queue.
     *
     * @param string $pfad Dateipfad oder ":memory:" für eine reine
     *                     Speicherdatenbank (praktisch im Test).
     */
    public static function oeffne(string $pfad): PDO
    {
        $pdo = new PDO('sqlite:' . $pfad, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // Konkurrieren mehrere Worker um dieselbe Datei, kann SQLite kurz
        // "database is locked" melden. Ein Wartelimit lässt PDO in diesem
        // Fall ein paar Sekunden erneut versuchen, statt sofort zu scheitern.
        $pdo->exec('PRAGMA busy_timeout = 5000');

        return $pdo;
    }
}
