<?php

declare(strict_types=1);

namespace App\Domain;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Der Vertrag für den Zugriff auf Abonnenten. Er gehört gedanklich zur
 * Domäne und ist in ihrer Sprache formuliert - existiertMit, speichere -,
 * nie in der Sprache der Datenbank. Von SQL, PDO oder SQLite steht hier
 * bewusst kein Wort. Die Infrastruktur erfüllt diesen Vertrag; die
 * Domäne gibt ihn vor. Das ist die umgekehrte Abhängigkeit aus
 * Kapitel 32, jetzt als tragende Säule der Architektur.
 */
interface AbonnentRepository
{
    /**
     * Ist unter dieser Adresse bereits jemand angemeldet?
     */
    public function existiertMit(string $email): bool;

    /**
     * Speichert einen Abonnenten und gibt ihn mit vergebener id zurück.
     */
    public function speichere(Abonnent $abonnent): Abonnent;
}
