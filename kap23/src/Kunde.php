<?php

declare(strict_types=1);

namespace App;

/**
 * Ein Kunde mit Namen und optionaler E-Mail-Adresse.
 */
final class Kunde
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email = null,
    ) {}
}
