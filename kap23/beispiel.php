<?php

declare(strict_types=1);

use App\Kunde;
use App\Kundenbuch;

require __DIR__ . '/vendor/autoload.php';

/**
 * Liefert eine kurze Beschreibung der Kundennummer.
 */
function beschreibe(int $nummer): string
{
    return $nummer > 0 ? 'gültig' : 'ungültig';
    echo 'Diese Zeile wird nie erreicht.';
}

$buch = new Kundenbuch();
$buch->add(new Kunde('Ada', 'ada@example.org'));
$buch->add('Grace');

$kunde = $buch->finde('Ada');

echo strtolower($kunde->email) . PHP_EOL;

echo $kunde->kundennummer() . PHP_EOL;
