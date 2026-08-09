<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

/*
 * Konfiguration für Rector. Aufruf aus dem Projektwurzel-Verzeichnis:
 *   vendor/bin/rector process --dry-run   (nur ansehen)
 *   vendor/bin/rector process             (anwenden)
 */
return RectorConfig::configure()
    // Welche Ordner Rector durchsucht. Mehrere Pfade sind möglich.
    ->withPaths([
        __DIR__ . '/src',
    ])
    // Hebt den Code auf den Sprachstand von PHP 8.4.
    ->withPhpSets(php84: true)
    // Vorgefertigte Qualitäts-Regelsätze zuschalten.
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
    );
