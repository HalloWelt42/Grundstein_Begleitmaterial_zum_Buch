<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Die drei Umgebungen, in denen dieselbe Anwendung läuft. Als wertbehaftetes
 * Enum (Kapitel 17) statt roher Zeichenketten: Ein Tippfehler wie 'produktion'
 * oder 'PROD' kommt gar nicht erst durch, und der Code kennt genau diese drei
 * Fälle - nicht mehr und nicht weniger.
 */
enum Umgebung: string
{
    case Entwicklung = 'dev';
    case Test = 'test';
    case Produktion = 'prod';
}
