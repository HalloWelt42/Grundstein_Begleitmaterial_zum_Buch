<?php

declare(strict_types=1);

namespace App\Application;

use RuntimeException;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Die gemeinsame Oberklasse aller fachlichen Fehler dieses
 * Anwendungsfalls. Sie ist in der Sprache der Anwendung formuliert, nicht
 * in der von HTTP: Der Dienst wirft "Bereits angemeldet", nicht "409".
 * Erst die Präsentationsschicht übersetzt solche Fehler in Statuscodes.
 */
abstract class AnmeldeFehler extends RuntimeException
{
}
