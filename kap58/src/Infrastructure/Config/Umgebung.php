<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Die möglichen Umgebungen der Anwendung als Enum (Kapitel 17, 57). Ein Enum
 * statt eines rohen string schließt Tippfehler aus: 'produktiv' oder 'PROD'
 * gibt es nicht, nur genau diese beiden Fälle.
 */
enum Umgebung: string
{
    case Entwicklung = 'entwicklung';
    case Produktion = 'produktion';
}
