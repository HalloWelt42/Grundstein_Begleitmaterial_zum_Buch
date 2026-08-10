<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

/*
 * Grundstein - Kapitel 51: Continuous Integration
 *
 * Eine kleine, sauber getippte Fachklasse als Prüfling für die
 * automatische Prüfkette. Sie berechnet den Preis nach einem
 * Prozent-Rabatt - bewusst schlicht gehalten, damit im Mittelpunkt
 * nicht die Fachlichkeit steht, sondern die Werkzeuge, die diesen Code
 * bei jedem Push von allein prüfen: Tests, statische Analyse und
 * Stil-Prüfung. Alle Beträge sind ganze Cent, um Rundungsfehler mit
 * Fließkommazahlen zu vermeiden.
 */
final class Rabatt
{
    /**
     * Wendet einen Prozent-Rabatt auf einen Centbetrag an und gibt den
     * neuen Preis in Cent zurück. Der Abzug wird kaufmännisch gerundet.
     */
    public function anwenden(int $preisCent, int $prozent): int
    {
        if ($preisCent < 0) {
            throw new InvalidArgumentException('Der Preis darf nicht negativ sein.');
        }

        if ($prozent < 0 || $prozent > 100) {
            throw new InvalidArgumentException('Der Rabatt muss zwischen 0 und 100 Prozent liegen.');
        }

        $abzug = (int) round($preisCent * $prozent / 100);

        return $preisCent - $abzug;
    }
}
