<?php

declare(strict_types=1);

namespace App;

use Iterator;

/*
 * Grundstein - Kapitel 62: Generatoren, Iteratoren und Fibers
 *
 * Ein von Hand geschriebener Iterator über einen Zahlenbereich. Er zeigt,
 * wie viel Zeremonie das Interface Iterator verlangt: fünf Methoden und
 * ein Positionszeiger, den man selbst verwaltet. Genau dasselbe erledigt
 * ein Generator in drei Zeilen (siehe iteratoren.php). Ein handgeschriebener
 * Iterator hat aber einen Vorzug, den ein Generator nicht hat: Er lässt
 * sich beliebig oft zurückspulen und erneut durchlaufen.
 *
 * @implements Iterator<int, int>
 */
final class Bereich implements Iterator
{
    /** Der aktuelle Wert an der jetzigen Position. */
    private int $aktuell;

    /** Ein laufender Index, der als Schlüssel dient. */
    private int $index = 0;

    public function __construct(
        private readonly int $von,
        private readonly int $bis,
        private readonly int $schritt = 1,
    ) {
        $this->aktuell = $von;
    }

    /** Der Wert an der jetzigen Position. */
    public function current(): int
    {
        return $this->aktuell;
    }

    /** Der Schlüssel zur jetzigen Position - hier der laufende Index. */
    public function key(): int
    {
        return $this->index;
    }

    /** Einen Schritt weiterrücken. */
    public function next(): void
    {
        $this->aktuell += $this->schritt;
        $this->index++;
    }

    /** Steht der Zeiger noch auf einem gültigen Wert? */
    public function valid(): bool
    {
        return $this->aktuell <= $this->bis;
    }

    /** Zurück an den Anfang - foreach ruft dies vor dem ersten Wert auf. */
    public function rewind(): void
    {
        $this->aktuell = $this->von;
        $this->index = 0;
    }
}
