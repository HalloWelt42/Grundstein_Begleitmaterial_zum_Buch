<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Ein Fake: eine echte, voll funktionsfähige Umsetzung des
 * BenutzerRepository. Sie folgt genau dem Muster des InMemory-Repositorys
 * aus Kapitel 32, nur für Benutzer statt Kunden - die Daten liegen in
 * einem Array im Speicher statt in einer Datenbank. Der Fake verhält sich
 * wie das echte Repository (findByEmail findet, save vergibt eine id),
 * kommt aber ohne PDO, Schema und Netzwerk aus. Damit läuft ein Test in
 * Sekundenbruchteilen und ist von nichts Äußerem abhängig.
 */
final class InMemoryBenutzerRepository implements BenutzerRepository
{
    /** @var array<int, Benutzer> */
    private array $benutzer = [];

    private int $naechsteId = 1;

    public function findByEmail(string $email): ?Benutzer
    {
        foreach ($this->benutzer as $vorhandener) {
            if ($vorhandener->email === $email) {
                return $vorhandener;
            }
        }

        return null;
    }

    public function save(Benutzer $benutzer): Benutzer
    {
        // Neue id vergeben, falls noch keine da ist - wie die Datenbank.
        $id = $benutzer->id ?? $this->naechsteId++;

        // readonly: statt zu verändern, ein neues Objekt mit id bauen.
        $gespeichert = new Benutzer(
            $id,
            $benutzer->email,
            $benutzer->token,
            $benutzer->registriertAm,
        );
        $this->benutzer[$id] = $gespeichert;

        return $gespeichert;
    }
}
