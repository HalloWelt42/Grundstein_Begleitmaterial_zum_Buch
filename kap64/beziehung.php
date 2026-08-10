<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Bestellhistorie;
use App\KundenRepository;

Bestellhistorie::$ladungen = 0;

$repository = new KundenRepository();
$kunde = $repository->finde(7, 'Ada Lovelace');

// Nur der Name interessiert - die teure Historie bleibt unberührt.
echo 'Kunde:  ' . $kunde->name . "\n";
echo 'Ladungen nach Anzeige: ' . Bestellhistorie::$ladungen . "\n";

// Jetzt fragt jemand nach der Historie - erst hier wird sie geladen.
echo 'Bestellungen: ' . $kunde->historie()->anzahl() . "\n";
echo 'Ladungen nach Zugriff: ' . Bestellhistorie::$ladungen . "\n";
