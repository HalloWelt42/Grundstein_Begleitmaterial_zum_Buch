<?php

declare(strict_types=1);

namespace App\Application;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Fachlicher Fehler: Unter dieser Adresse ist bereits jemand angemeldet.
 * Der Anwendungsdienst wirft ihn, wenn die Fachregel "keine doppelte
 * Anmeldung" verletzt wird; die Präsentationsschicht macht daraus einen
 * Statuscode 409.
 */
final class BereitsAngemeldet extends AnmeldeFehler
{
}
