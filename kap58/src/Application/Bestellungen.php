<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Bestellung;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein Port (Kapitel 55): ein Vertrag, den die Anwendung besitzt und in ihrer
 * eigenen Sprache formuliert. Kein Wort von SQL oder PDO steht hier - nur was
 * die Anwendung von einem Speicher erwartet. Erfüllt wird der Port außen in
 * der Infrastruktur, einmal per Datenbank und einmal im Arbeitsspeicher.
 */
interface Bestellungen
{
    // Findet genau eine Bestellung oder null, wenn es sie nicht gibt.
    public function find(int $id): ?Bestellung;

    // Speichert eine Bestellung und gibt sie mit gültiger id zurück.
    public function save(Bestellung $bestellung): Bestellung;

    /** @return list<Bestellung> Alle Bestellungen, nach id sortiert. */
    public function alle(): array;
}
