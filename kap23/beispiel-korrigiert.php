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
}

$buch = new Kundenbuch();
$buch->add(new Kunde('Ada', 'ada@example.org'));
$buch->add(new Kunde('Grace'));

$kunde = $buch->finde('Ada');

// Erst prüfen, dann zugreifen: PHPStan versteht die Verengung auf non-null.
if ($kunde !== null && $kunde->email !== null) {
    echo strtolower($kunde->email) . PHP_EOL;
}
