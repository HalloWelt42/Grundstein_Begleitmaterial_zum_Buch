<?php

declare(strict_types=1);

namespace App\Application;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein Eingabeobjekt (Command-DTO, Kapitel 53) an der Grenze zur Anwendung.
 * Es trägt die rohe Absicht - "diese Adresse möchte für diesen Betrag
 * bestellen" - und entkoppelt den Dienst von der Form, in der die Daten
 * hereinkamen: Formular, JSON oder Kommandozeile.
 */
final readonly class BestellungAufgeben
{
    public function __construct(
        public string $kunde,
        public float $euro,
    ) {}
}
