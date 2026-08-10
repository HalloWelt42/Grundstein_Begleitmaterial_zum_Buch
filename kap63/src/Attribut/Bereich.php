<?php

declare(strict_types=1);

namespace App\Attribut;

use App\Regel;
use Attribute;

/*
 * Grundstein - Kapitel 63: Reflection und Attribute
 *
 * Ein Attribut mit zwei Argumenten. #[Bereich(18, 120)] prüft, ob eine
 * Ganzzahl innerhalb der Grenzen liegt (jeweils einschließlich).
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Bereich implements Regel
{
    public function __construct(
        public readonly int $min,
        public readonly int $max,
    ) {}

    public function pruefe(mixed $wert): ?string
    {
        if (is_int($wert) && ($wert < $this->min || $wert > $this->max)) {
            return "muss zwischen {$this->min} und {$this->max} liegen";
        }

        return null;
    }
}
