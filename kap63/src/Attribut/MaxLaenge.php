<?php

declare(strict_types=1);

namespace App\Attribut;

use App\Regel;
use Attribute;

/*
 * Grundstein - Kapitel 63: Reflection und Attribute
 *
 * Ein Attribut mit einem Argument. Der Wert in #[MaxLaenge(50)] landet
 * im Konstruktor und wird zur Eigenschaft $laenge. Beim Auslesen baut
 * newInstance() genau dieses Objekt - mit der im Code notierten Zahl.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class MaxLaenge implements Regel
{
    public function __construct(public readonly int $laenge) {}

    public function pruefe(mixed $wert): ?string
    {
        // mb_strlen zählt Zeichen, nicht Bytes - wichtig für Umlaute.
        if (is_string($wert) && mb_strlen($wert) > $this->laenge) {
            return "darf höchstens {$this->laenge} Zeichen haben";
        }

        return null;
    }
}
