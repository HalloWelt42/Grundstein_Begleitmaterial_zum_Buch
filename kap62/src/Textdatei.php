<?php

declare(strict_types=1);

namespace App;

use Generator;
use RuntimeException;

/*
 * Grundstein - Kapitel 62: Generatoren, Iteratoren und Fibers
 *
 * Ein schmaler Helfer, der eine Textdatei Zeile für Zeile ausliefert -
 * ohne sie je ganz in den Speicher zu laden. Genau das ist die Stärke
 * eines Generators: Er hält immer nur die eine Zeile, die gerade
 * gebraucht wird, egal ob die Datei ein Kilobyte oder ein Gigabyte hat.
 */
final class Textdatei
{
    private function __construct()
    {
        // Reiner Helfer - es gibt nichts zu erzeugen.
    }

    /**
     * Liefert die Zeilen der Datei nacheinander. Der Schlüssel ist die
     * Zeilennummer (ab 1), der Wert die Zeile ohne den Zeilenumbruch am
     * Ende. Weil die Funktion ein yield enthält, ist sie ein Generator:
     * Bei jedem Schleifendurchlauf des Aufrufers wird genau eine weitere
     * Zeile gelesen und herausgegeben.
     *
     * @return Generator<int, string>
     */
    public static function zeilen(string $pfad): Generator
    {
        $griff = fopen($pfad, 'rb');
        if ($griff === false) {
            throw new RuntimeException("Kann Datei nicht öffnen: {$pfad}");
        }

        try {
            $nummer = 0;
            // fgets holt genau eine Zeile aus dem Datei-Strom (Kapitel 29).
            while (($zeile = fgets($griff)) !== false) {
                yield ++$nummer => rtrim($zeile, "\r\n");
            }
        } finally {
            // Egal wie die Schleife endet - auch wenn der Aufrufer vorzeitig
            // abbricht: Der Datei-Griff wird zuverlässig geschlossen.
            fclose($griff);
        }
    }
}
