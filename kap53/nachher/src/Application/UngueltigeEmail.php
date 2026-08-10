<?php

declare(strict_types=1);

namespace App\Application;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Fachlicher Fehler: Die übergebene Adresse ist syntaktisch keine
 * gültige E-Mail. Der Anwendungsdienst wirft diesen Fehler; die
 * Präsentationsschicht macht daraus einen Statuscode 422.
 */
final class UngueltigeEmail extends AnmeldeFehler
{
}
