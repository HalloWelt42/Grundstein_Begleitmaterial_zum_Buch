<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\CodeQuelle;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben
 *
 * Ein handgeschriebener Stub für den Zufall: Er gibt statt eines
 * zufälligen immer denselben, im Konstruktor festgelegten Code zurück.
 * Damit ist der erzeugte Code im Test vorhersagbar und prüfbar.
 */
final class FesteCodeQuelle implements CodeQuelle
{
    public function __construct(private readonly string $code) {}

    public function naechster(): string
    {
        return $this->code;
    }
}
