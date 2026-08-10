<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Der Vertrag für den Datenzugriff auf Benutzer - wie in Kapitel 32 ein
 * eigenes Interface, das kein Wort über PDO, SQL oder eine konkrete
 * Datenbank verliert. Genau diese Grenze macht es später möglich, im
 * Test einen handgeschriebenen Fake einzusetzen.
 */
interface BenutzerRepository
{
    // Findet einen Benutzer über seine E-Mail-Adresse oder null.
    public function findByEmail(string $email): ?Benutzer;

    // Speichert einen Benutzer und gibt ihn mit gültiger id zurück.
    public function save(Benutzer $benutzer): Benutzer;
}
