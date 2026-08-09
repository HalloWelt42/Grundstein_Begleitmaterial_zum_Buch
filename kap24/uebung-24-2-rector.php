<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php54\Rector\Array_\LongArrayToShortArrayRector;

/*
 * Lösung zu Übung 24.2: nur die kurze Array-Syntax herstellen.
 * Es wird genau eine Regel per withRules aktiviert; alles andere
 * bleibt unangetastet.
 */
return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withRules([
        // Nur die kurze Array-Syntax herstellen:
        LongArrayToShortArrayRector::class,
    ]);
