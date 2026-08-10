<?php

declare(strict_types=1);

namespace App;

interface Rabatt
{
    // Liefert den Abzug in Cent für eine gegebene Zwischensumme.
    public function abzug(int $zwischensumme): int;
}
