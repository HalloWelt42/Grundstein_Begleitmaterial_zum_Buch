<?php

declare(strict_types=1);

namespace App;

final class Position
{
    public function __construct(
        public readonly string $name,
        public readonly int $einzelpreis,
        public readonly int $menge,
    ) {}

    // Gesamtpreis dieser Position in Cent.
    public function gesamtpreis(): int
    {
        return $this->einzelpreis * $this->menge;
    }
}
