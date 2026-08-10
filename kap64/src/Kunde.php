<?php

declare(strict_types=1);

namespace App;

/**
 * Ein Kunde ist billig zu bauen: eine Kennung und ein Name. Seine
 * Bestellhistorie dagegen ist teuer - deshalb bekommt der Kunde sie als
 * (möglicherweise faulen) Platzhalter hereingereicht und berührt sie erst,
 * wenn wirklich jemand danach fragt.
 */
final class Kunde
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        private readonly Bestellhistorie $historie,
    ) {}

    public function historie(): Bestellhistorie
    {
        return $this->historie;
    }
}
