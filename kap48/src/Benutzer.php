<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Ein Benutzer als getipptes, unveränderliches Objekt (readonly aus
 * Kapitel 13). Er trägt neben E-Mail-Adresse und id einen
 * Bestätigungscode sowie den Zeitpunkt der Registrierung. Die id ist
 * nullable, weil ein frisch angelegter Benutzer noch keine hat - die
 * vergibt erst das Repository beim Speichern (wie in Kapitel 32).
 */
final class Benutzer
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $email,
        public readonly string $token,
        public readonly DateTimeImmutable $registriertAm,
    ) {}
}
