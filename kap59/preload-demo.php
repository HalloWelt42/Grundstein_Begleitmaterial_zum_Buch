<?php

declare(strict_types=1);

/*
 * Führt die vorgeladenen Klassen vor. Bemerkenswert ist, was hier NICHT
 * steht: kein require, kein Autoloader. Trotzdem stehen Preisrechner und
 * Rabatt bereit - weil das Preload-Skript sie beim Start dauerhaft in den
 * Speicher gelegt hat.
 *
 * Gestartet wird die Datei mit vorgeschaltetem Preloading:
 *   php -dopcache.enable_cli=1 -dopcache.preload=/app/preload.php preload-demo.php
 */

echo 'Brutto: ' . (new App\Preisrechner())->brutto(10000) . PHP_EOL;
echo 'Rabatt: ' . (new App\Rabatt())->anwenden(10000, 25.0) . PHP_EOL;
