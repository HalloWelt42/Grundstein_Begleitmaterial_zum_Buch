<?php

declare(strict_types=1);

namespace App;

use App\Attribut\Bereich;
use App\Attribut\MaxLaenge;
use App\Attribut\NichtLeer;

/*
 * Grundstein - Kapitel 63: Reflection und Attribute
 *
 * Ein Datenobjekt, an dessen Feldern die Regeln direkt als Attribute
 * stehen. Die Prüfvorschrift liegt damit unmittelbar neben dem Wert, den
 * sie betrifft - nicht in einer entfernten Konfigurationsdatei. Die
 * Attribute sitzen an den promoteten Konstruktor-Parametern und gelten
 * für die daraus entstehenden Eigenschaften.
 */
final class Registrierung
{
    public function __construct(
        #[NichtLeer]
        #[MaxLaenge(30)]
        public readonly string $name,

        #[NichtLeer]
        #[MaxLaenge(50)]
        public readonly string $email,

        #[Bereich(18, 120)]
        public readonly int $alter,
    ) {}
}
