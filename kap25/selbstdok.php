<?php

declare(strict_types=1);

/**
 * Eine Bestellzeile mit Menge und Einzelpreis in Cent.
 */
final class Bestellzeile
{
    public function __construct(
        public readonly int $menge,
        public readonly int $einzelpreisCent,
    ) {}
}

/**
 * Summiert die Zeilen einer Bestellung zu einem Gesamtbetrag in Cent.
 *
 * @param list<Bestellzeile> $zeilen
 */
function gesamtbetragCent(array $zeilen): int
{
    $summe = 0;
    foreach ($zeilen as $zeile) {
        $summe += $zeile->menge * $zeile->einzelpreisCent;
    }

    return $summe;
}

$zeilen = [
    new Bestellzeile(2, 499),
    new Bestellzeile(1, 1299),
];

echo gesamtbetragCent($zeilen) . PHP_EOL;
