<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 4: Jeder Lauf beginnt bei null.
 *
 * Eine Funktion mit einer statischen Variablen zählt innerhalb EINES
 * Laufs hoch. Rufst du das Skript ein zweites Mal auf, fängt der
 * Zähler wieder bei 1 an - denn zwischen zwei Läufen teilt PHP keinen
 * Speicher. Genau das meint "shared nothing".
 */

/**
 * Gibt bei jedem Aufruf die nächste laufende Nummer zurück.
 * Die statische Variable lebt nur, solange dieser eine Lauf dauert.
 */
function naechsteNummer(): int
{
    static $zaehler = 0;   // beim Skriptstart immer wieder 0.
    return ++$zaehler;
}

echo naechsteNummer() . PHP_EOL;
echo naechsteNummer() . PHP_EOL;
echo naechsteNummer() . PHP_EOL;
