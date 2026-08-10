<?php

declare(strict_types=1);

namespace App\Cache;

/**
 * Steht stellvertretend für eine teure Quelle - etwa eine schwere
 * Datenbankauswertung. Die Berechnung summiert alle Primzahlen bis zu einer
 * Grenze und durchläuft dazu ein ganzes Zahlensieb; das dauert spürbar.
 *
 * Ein Zähler hält fest, wie oft wirklich gerechnet wurde. So lässt sich
 * beweisen, dass der Cache die teure Arbeit tatsächlich einspart.
 */
final class StatistikDienst
{
    private int $berechnungen = 0;

    /**
     * Summiert alle Primzahlen bis einschließlich $grenze mit dem Sieb des
     * Eratosthenes.
     */
    public function summeDerPrimzahlenBis(int $grenze): int
    {
        $this->berechnungen++;

        // Sieb des Eratosthenes: Jede Zahl gilt zunächst als Primzahl.
        $istKeinePrimzahl = array_fill(0, $grenze + 1, false);
        $summe = 0;

        for ($zahl = 2; $zahl <= $grenze; $zahl++) {
            if ($istKeinePrimzahl[$zahl]) {
                continue;
            }

            $summe += $zahl;

            // Alle Vielfachen der Primzahl als "keine Primzahl" markieren.
            for ($vielfaches = $zahl * $zahl; $vielfaches <= $grenze; $vielfaches += $zahl) {
                $istKeinePrimzahl[$vielfaches] = true;
            }
        }

        return $summe;
    }

    /** Wie oft wurde wirklich gerechnet? */
    public function anzahlBerechnungen(): int
    {
        return $this->berechnungen;
    }
}
