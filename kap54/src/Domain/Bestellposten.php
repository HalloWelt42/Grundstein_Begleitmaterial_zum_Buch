<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Ein einzelner Posten einer Bestellung als Wertobjekt: eine Bezeichnung,
 * ein Einzelpreis (selbst ein Wertobjekt) und eine Menge. Ein Posten hat
 * keine Identität - zwei Posten mit gleicher Bezeichnung, gleichem Preis
 * und gleicher Menge sind austauschbar. Er gehört untrennbar zur Bestellung
 * und lebt nur innerhalb ihres Aggregats.
 */
final readonly class Bestellposten
{
    public function __construct(
        public string $bezeichnung,
        public Geldbetrag $einzelpreis,
        public int $menge,
    ) {
        if (trim($bezeichnung) === '') {
            throw new InvalidArgumentException('Ein Posten braucht eine Bezeichnung.');
        }

        if ($menge < 1) {
            throw new InvalidArgumentException(
                "Die Menge muss mindestens 1 sein, {$menge} gegeben."
            );
        }
    }

    /**
     * Der Zwischenpreis dieses Postens: Einzelpreis mal Menge. Die Rechnung
     * bleibt im Wertobjekt Geldbetrag und liefert wieder einen Geldbetrag.
     */
    public function zwischensumme(): Geldbetrag
    {
        return $this->einzelpreis->mal($this->menge);
    }
}
