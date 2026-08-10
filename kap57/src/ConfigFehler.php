<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Die eine Ausnahme, mit der die Konfiguration jeden Fehler meldet: ein
 * fehlender Pflichtwert, eine unbekannte Umgebung, eine verletzte Invariante.
 *
 * Regel für die Meldungen: Sie nennen immer nur den SCHLÜSSEL (etwa
 * "API_KEY"), niemals den Wert dahinter. So kann kein Geheimnis versehentlich
 * in einer Fehlermeldung, einem Log oder einer Stapelverfolgung landen.
 */
final class ConfigFehler extends RuntimeException
{
}
