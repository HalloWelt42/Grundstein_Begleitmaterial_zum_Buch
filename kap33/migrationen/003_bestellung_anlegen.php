<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 33: Migrationen
 *
 * Eine Migration im PHP-Format. Anders als eine reine SQL-Datei ist sie
 * umkehrbar: up() legt die Tabelle bestellung an, down() entfernt sie
 * wieder. Die Datei gibt ein Objekt zurück, das den Vertrag Migration
 * erfüllt - der Runner ruft up() oder down() darauf auf.
 */

use App\Migration\Migration;

return new class implements Migration {
    public function up(PDO $pdo): void
    {
        // Die "viele" Seite: beliebig viele Bestellungen je Kunde.
        $pdo->exec(
            'CREATE TABLE bestellung (
                id          INTEGER PRIMARY KEY,
                kunde_id    INTEGER NOT NULL,
                artikel     TEXT    NOT NULL,
                betrag_cent INTEGER NOT NULL,
                FOREIGN KEY (kunde_id) REFERENCES kunde (id)
            )'
        );
    }

    public function down(PDO $pdo): void
    {
        // Der genaue Gegenschritt zu up(): die Tabelle wieder entfernen.
        $pdo->exec('DROP TABLE bestellung');
    }
};
