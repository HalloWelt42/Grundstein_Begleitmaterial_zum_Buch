<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\Domain\Abonnent;
use App\Domain\AbonnentRepository;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Test-Double)
 *
 * Eine zweite Umsetzung desselben Vertrags - ganz ohne Datenbank, nur mit
 * einem Array im Speicher (Fake aus Kapitel 32 und 48). Weil der
 * Anwendungsdienst nur den Vertrag AbonnentRepository kennt, merkt er
 * nicht, dass hier kein PDO arbeitet. So läuft sein Test in
 * Sekundenbruchteilen und braucht kein Schema.
 */
final class InMemoryAbonnentRepository implements AbonnentRepository
{
    /** @var list<Abonnent> */
    private array $abonnenten = [];

    private int $naechsteId = 1;

    public function existiertMit(string $email): bool
    {
        foreach ($this->abonnenten as $abonnent) {
            if ($abonnent->email === $email) {
                return true;
            }
        }

        return false;
    }

    public function speichere(Abonnent $abonnent): Abonnent
    {
        $gespeichert = new Abonnent(
            $this->naechsteId++,
            $abonnent->email,
            $abonnent->angemeldetAm,
        );
        $this->abonnenten[] = $gespeichert;

        return $gespeichert;
    }

    /**
     * Nur für die Tests: alle bisher gespeicherten Abonnenten.
     *
     * @return list<Abonnent>
     */
    public function alle(): array
    {
        return $this->abonnenten;
    }
}
