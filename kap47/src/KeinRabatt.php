<?php

declare(strict_types=1);

namespace App;

final class KeinRabatt implements Rabatt
{
    // Der leere Rabatt: zieht nie etwas ab. Bequem als Standardwert.
    public function abzug(int $zwischensumme): int
    {
        return 0;
    }
}
