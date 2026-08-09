<?php

/*
 * Grundstein - Kapitel 5: Das Standardverhalten (schwache Typisierung).
 *
 * ACHTUNG - AUSNAHME: Diese eine Datei lässt declare(strict_types=1)
 * BEWUSST weg, um zu zeigen, wie PHP sich OHNE den strikten Modus
 * verhält. In jeder anderen Datei dieses Buches steht die Zeile ganz
 * oben. Hier soll sie gerade fehlen, damit der Unterschied sichtbar wird.
 *
 * Ohne strikten Modus wandelt PHP Werte bei Bedarf still um. Das ist
 * bequem - und genau deshalb gefährlich, weil Tippfehler unbemerkt
 * durchrutschen.
 */

/**
 * Erwartet zwei Ganzzahlen und gibt ihre Summe zurück.
 */
function addiere(int $a, int $b): int
{
    return $a + $b;
}

// Der String "42" ist keine Ganzzahl - ohne strikten Modus wandelt PHP
// ihn hier still in die Zahl 42 um, und niemand merkt etwas davon.
$summe = addiere('42', 8);
echo 'addiere("42", 8) = ' . $summe . PHP_EOL;
