<?php

declare(strict_types=1);

namespace App\Domain;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein wertbehaftetes Enum (Kapitel 17). Es schließt falsche Zustände von
 * vornherein aus: Eine Bestellung ist entweder neu oder bezahlt, nichts
 * dazwischen. Der zugrunde liegende string-Wert wandert unverändert in die
 * Datenbank und zurück.
 */
enum Bestellstatus: string
{
    case Neu = 'neu';
    case Bezahlt = 'bezahlt';
}
