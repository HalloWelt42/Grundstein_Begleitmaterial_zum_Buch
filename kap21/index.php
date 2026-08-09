<?php

declare(strict_types=1);

// Eine einzige Zeile genügt: Composer hat den Autoloader erzeugt.
require __DIR__ . '/vendor/autoload.php';

use App\Cart\Cart;
use App\Cart\Money;

$warenkorb = new Cart();
$warenkorb->add('Mechanische Tastatur', new Money(8990));
$warenkorb->add('USB-C-Kabel', new Money(1290));

echo 'Warenkorb ' . $warenkorb->id() . "\n";
echo $warenkorb->beleg() . "\n";
