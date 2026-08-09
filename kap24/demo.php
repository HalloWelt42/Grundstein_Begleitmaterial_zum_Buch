<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Warenkorb;

/*
 * Kleiner Treiber, der zeigt, dass die Klasse sich vor und nach der
 * Rector-Modernisierung exakt gleich verhält. Aufruf:
 *   php demo.php
 */
$korb = new Warenkorb('EUR');
$korb->hinzufuegen('Tastatur', 4990);
$korb->hinzufuegen('Maus', 2500);

echo $korb->zusammenfassung() . PHP_EOL;
echo ($korb->enthält('Maus') ? 'Maus enthalten' : 'keine Maus') . PHP_EOL;
