<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 63: Reflection und Attribute
 *
 * Ein unveränderliches Wertobjekt für einen einzelnen Regelverstoß:
 * welche Eigenschaft betroffen ist und was an ihr nicht stimmt. Der
 * Validierer sammelt eine Liste dieser Objekte, statt roher Arrays.
 */
final class Verstoss
{
    public function __construct(
        public readonly string $eigenschaft,
        public readonly string $meldung,
    ) {}

    public function alsText(): string
    {
        return "{$this->eigenschaft}: {$this->meldung}";
    }
}
