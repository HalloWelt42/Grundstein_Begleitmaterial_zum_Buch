<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 18: Namespaces und Autoloading
 *
 * Einstiegspunkt. Der Autoloader wird EINMAL eingebunden - danach lädt
 * PHP jede benötigte Klasse selbst nach, ohne ein einziges require pro
 * Klasse.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

// Im echten Projekt steht hier der von Composer erzeugte Loader:
//   require __DIR__ . '/vendor/autoload.php';
// Ohne Composer nutzen wir den handgeschriebenen Loader - er tut dasselbe:
require __DIR__ . '/autoload.php';

use App\Catalog\Product;
use App\Order\Order;

$order = new Order();
$order->add(new Product('Tastatur', 4990));
$order->add(new Product('Maus', 2500));

echo $order->receipt() . "\n";
