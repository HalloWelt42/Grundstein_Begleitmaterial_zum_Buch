<?php

declare(strict_types=1);

/*
 * Ein Preload-Skript. Es läuft genau EINMAL, wenn der Server startet, und
 * legt die angegebenen Klassen dauerhaft in den Speicher. Ab dann ist jede
 * dieser Klassen in jeder Anfrage sofort da - ohne require, ohne Autoloader,
 * ohne erneutes Kompilieren.
 *
 * Aktiviert wird das Skript über eine einzige Zeile in der php.ini:
 *   opcache.preload=/app/preload.php
 *
 * Wir laden hier ein ganzes Verzeichnis. require_once lädt jede Klasse so,
 * dass sie samt ihrer Bezüge fest im Speicher steht.
 */

$verzeichnis = __DIR__ . '/src';

$anzahl = 0;
foreach (new DirectoryIterator($verzeichnis) as $eintrag) {
    if ($eintrag->isFile() && $eintrag->getExtension() === 'php') {
        require_once $eintrag->getPathname();
        $anzahl++;
    }
}

// Diese Meldung erscheint einmalig im Server-Protokoll beim Start.
error_log("Preloading: {$anzahl} Dateien vorgeladen.");
