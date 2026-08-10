<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Die innerste Schicht: die Domäne. Ein Abonnent ist ein fachlicher
 * Gegenstand - ein getipptes, unveränderliches Datenobjekt (readonly
 * aus Kapitel 13). Er weiß nichts von HTTP, nichts von SQL, nichts von
 * PDO. Genau diese Unwissenheit macht ihn stabil: Er hängt an nichts,
 * also kann nichts von außen ihn zum Wackeln bringen.
 */
final class Abonnent
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $email,
        public readonly DateTimeImmutable $angemeldetAm,
    ) {}

    /**
     * Baut einen noch nicht gespeicherten Abonnenten - ohne id, denn die
     * vergibt erst die Infrastruktur beim Speichern.
     */
    public static function neu(string $email, DateTimeImmutable $angemeldetAm): self
    {
        return new self(null, $email, $angemeldetAm);
    }
}
