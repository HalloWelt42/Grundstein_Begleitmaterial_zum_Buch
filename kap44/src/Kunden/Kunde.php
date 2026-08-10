<?php

declare(strict_types=1);

namespace Grundstein\Kunden;

/**
 * Ein Kunde als getipptes, unveränderliches Objekt - übernommen aus dem
 * Repository-Kapitel 32. Die id ist ?int, weil ein frisch angelegter Kunde
 * noch keine hat: Die vergibt erst die Datenbank beim Einfügen.
 */
final class Kunde
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly int $umsatzCent,
    ) {
    }

    /** Baut einen noch nicht gespeicherten Kunden ohne id. */
    public static function neu(string $name, string $email, int $umsatzCent = 0): self
    {
        return new self(null, $name, $email, $umsatzCent);
    }
}
