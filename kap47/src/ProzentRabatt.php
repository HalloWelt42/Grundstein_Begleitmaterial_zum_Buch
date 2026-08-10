<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

final class ProzentRabatt implements Rabatt
{
    public function __construct(
        private readonly int $prozent,
    ) {
        // Ein Prozentsatz außerhalb von 0 bis 100 ist ein Programmierfehler.
        if ($prozent < 0 || $prozent > 100) {
            throw new InvalidArgumentException(
                "Prozentsatz muss zwischen 0 und 100 liegen, {$prozent} gegeben.",
            );
        }
    }

    public function abzug(int $zwischensumme): int
    {
        // Ganzzahlig rechnen, damit keine Cent-Bruchteile entstehen.
        return intdiv($zwischensumme * $this->prozent, 100);
    }
}
