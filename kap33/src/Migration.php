<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 33: Migrationen
 *
 * Der Vertrag für eine umkehrbare Migration in PHP-Form. Wer ihn
 * erfüllt, sagt zu: up() bewegt das Schema vorwärts, down() macht
 * genau diesen Schritt wieder rückgängig.
 */

namespace App\Migration;

use PDO;

interface Migration
{
    /**
     * Bewegt das Schema einen Schritt vorwärts (z. B. Tabelle anlegen).
     */
    public function up(PDO $pdo): void;

    /**
     * Nimmt genau diesen Schritt wieder zurück (z. B. Tabelle entfernen).
     */
    public function down(PDO $pdo): void;
}
