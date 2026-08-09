<?php

declare(strict_types=1);

namespace App;

class Warenkorb
{
    private array $positionen = [];

    public function hinzufuegen(string $name, int $preisCents): void
    {
        $this->positionen[] = ['name' => $name, 'preis' => $preisCents];
    }

    public function summe(): int
    {
        $summe = 0;
        foreach ($this->positionen as $position) {
            $summe += $position['preis'];
        }
        return $summe;
    }
}
