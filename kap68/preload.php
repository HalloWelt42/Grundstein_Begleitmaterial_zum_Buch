<?php

declare(strict_types=1);

/*
 * Preload-Skript (Kapitel 59). Es läuft genau einmal, wenn FPM startet,
 * und legt die eigenen Klassen dauerhaft in den Speicher. Ab dann ist
 * jede davon in jeder Anfrage sofort da - ohne Autoloader, ohne erneutes
 * Kompilieren. Aktiviert über opcache.preload in der opcache.ini.
 */

$verzeichnis = __DIR__ . '/src';

$anzahl = 0;
foreach (new DirectoryIterator($verzeichnis) as $eintrag) {
    if ($eintrag->isFile() && $eintrag->getExtension() === 'php') {
        require_once $eintrag->getPathname();
        ++$anzahl;
    }
}

// Diese Meldung erscheint einmalig im Protokoll beim Start.
error_log("Preloading: {$anzahl} Dateien vorgeladen.");
