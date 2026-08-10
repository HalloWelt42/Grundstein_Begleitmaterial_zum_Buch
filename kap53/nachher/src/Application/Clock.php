<?php

declare(strict_types=1);

namespace App\Application;

use DateTimeImmutable;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Der schmale Vertrag für die aktuelle Zeit - dieselbe Naht wie in
 * Kapitel 49. Der Anwendungsdienst fragt seine Uhr nach dem Zeitpunkt,
 * statt new DateTimeImmutable() selbst aufzurufen. In Produktion steckt
 * dahinter die Systemuhr, im Test eine feste Uhr.
 */
interface Clock
{
    public function jetzt(): DateTimeImmutable;
}
