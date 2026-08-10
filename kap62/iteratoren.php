<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Bereich;

/*
 * Grundstein - Kapitel 62: Iteratoren von Hand und als Generator
 *
 * Drei Wege, dasselbe durchlaufbare Ding zu bauen - und zu sehen, wie
 * viel Arbeit ein Generator einem abnimmt.
 */

// 1) Der von Hand geschriebene Iterator aus src/Bereich.php: fünf
//    Methoden Zeremonie - aber ganz gewöhnlich mit foreach benutzbar.
echo 'Bereich (Iterator):            ';
foreach (new Bereich(0, 10, 2) as $wert) {
    echo $wert . ' ';
}
echo PHP_EOL;

// 2) Ein Generator liefert genau dasselbe in drei Zeilen.
function bereich(int $von, int $bis, int $schritt = 1): Generator
{
    for ($i = $von; $i <= $bis; $i += $schritt) {
        yield $i;
    }
}

echo 'bereich (Generator):           ';
foreach (bereich(0, 10, 2) as $wert) {
    echo $wert . ' ';
}
echo PHP_EOL;

// 3) IteratorAggregate: eine Klasse wird durchlaufbar, indem sie das
//    Durchlaufen an einen Generator delegiert - statt selbst die fünf
//    Iterator-Methoden zu füllen.
final class Warenkorb implements IteratorAggregate
{
    /** @param list<string> $artikel */
    public function __construct(private array $artikel = [])
    {
    }

    public function getIterator(): Traversable
    {
        // yield from macht diese Methode selbst zu einem Generator.
        yield from $this->artikel;
    }
}

echo 'Warenkorb (IteratorAggregate): ';
foreach (new Warenkorb(['Schraube', 'Mutter', 'Dübel']) as $artikel) {
    echo $artikel . ' ';
}
echo PHP_EOL;
