<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

/**
 * Eine einzelne Position im Warenkorb: ein Artikel mit Einzelpreis und
 * Menge. Der Preis wird in Cent gehalten, damit keine Rundungsfehler
 * durch Gleitkommazahlen entstehen.
 */
final class Position
{
    public function __construct(
        public readonly string $name,
        public readonly int $einzelpreisCent,
        public readonly int $menge,
    ) {
        if ($einzelpreisCent < 0) {
            throw new InvalidArgumentException('Der Einzelpreis darf nicht negativ sein.');
        }

        if ($menge < 1) {
            throw new InvalidArgumentException('Die Menge muss mindestens 1 sein.');
        }
    }

    /**
     * Der Gesamtpreis dieser Position in Cent: Einzelpreis mal Menge.
     */
    public function gesamtpreisCent(): int
    {
        return $this->einzelpreisCent * $this->menge;
    }
}
