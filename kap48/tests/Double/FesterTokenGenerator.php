<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\TokenGenerator;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Ein handgeschriebener Stub für den Zufall: erzeuge() gibt stets den
 * festen Wert zurück, den der Konstruktor bekommen hat. Damit ist der
 * Bestätigungscode im Test vorhersagbar und lässt sich prüfen.
 */
final class FesterTokenGenerator implements TokenGenerator
{
    public function __construct(private readonly string $token) {}

    public function erzeuge(): string
    {
        return $this->token;
    }
}
