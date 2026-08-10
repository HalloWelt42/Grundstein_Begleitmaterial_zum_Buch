<?php

declare(strict_types=1);

namespace App;

use Generator;

/*
 * Grundstein - Kapitel 62: Generatoren, Iteratoren und Fibers
 *
 * Ein paar faule Bausteine für Ströme. Jeder von ihnen ist ein
 * Generator, der seine Eingabe durchreicht und dabei genau einen
 * Arbeitsschritt macht - erst wenn der Aufrufer zieht. So lassen sich
 * map, filter und "nimm die ersten N" verketten, ohne dass zwischendrin
 * je eine vollständige Zwischenliste im Speicher entsteht.
 */
final class Faul
{
    private function __construct()
    {
        // Reine Sammlung statischer Bausteine - nichts zu erzeugen.
    }

    /**
     * Bildet jeden Wert mit $fn ab. Die Schlüssel bleiben erhalten. Faul:
     * Es wird erst gerechnet, wenn der Aufrufer den nächsten Wert zieht.
     *
     * @template TKey
     * @template TIn
     * @template TOut
     * @param iterable<TKey, TIn>  $eingabe
     * @param callable(TIn): TOut  $fn
     * @return Generator<TKey, TOut>
     */
    public static function map(iterable $eingabe, callable $fn): Generator
    {
        foreach ($eingabe as $schluessel => $wert) {
            yield $schluessel => $fn($wert);
        }
    }

    /**
     * Lässt nur die Werte durch, für die $fn wahr liefert. Die Schlüssel
     * bleiben erhalten, werden dadurch aber lückenhaft.
     *
     * @template TKey
     * @template TValue
     * @param iterable<TKey, TValue>   $eingabe
     * @param callable(TValue): bool   $fn
     * @return Generator<TKey, TValue>
     */
    public static function filter(iterable $eingabe, callable $fn): Generator
    {
        foreach ($eingabe as $schluessel => $wert) {
            if ($fn($wert)) {
                yield $schluessel => $wert;
            }
        }
    }

    /**
     * Nimmt höchstens $anzahl Werte vom Anfang und hört dann auf. Das
     * return beendet den Generator - und damit zieht er auch aus einer
     * unendlichen Quelle nur so viel, wie wirklich gebraucht wird.
     *
     * @template TKey
     * @template TValue
     * @param iterable<TKey, TValue> $eingabe
     * @return Generator<TKey, TValue>
     */
    public static function nimm(iterable $eingabe, int $anzahl): Generator
    {
        if ($anzahl <= 0) {
            return;
        }

        $gezaehlt = 0;
        foreach ($eingabe as $schluessel => $wert) {
            yield $schluessel => $wert;

            if (++$gezaehlt >= $anzahl) {
                return; // genug - die Quelle wird nicht weiter gezogen
            }
        }
    }
}
