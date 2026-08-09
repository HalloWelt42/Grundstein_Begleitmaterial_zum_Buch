<?php

declare(strict_types=1);

final class Preisrechner
{
    /**
     * Berechnet den Bruttopreis aus einem Nettopreis.
     *
     * @deprecated Nutze stattdessen brutto(), das mit Centbeträgen rechnet
     *             und Rundungsfehler vermeidet.
     */
    public function bruttoAlt(float $netto): float
    {
        return $netto * 1.19;
    }

    /**
     * Berechnet den Bruttopreis aus einem Nettobetrag in Cent.
     */
    public function brutto(int $nettoCent): int
    {
        return (int) round($nettoCent * 1.19);
    }
}

// Das PHP-8.4-Attribut meldet die Verwendung zur Laufzeit als Deprecation.
#[\Deprecated(message: 'Nutze brutto() der Klasse Preisrechner.', since: '2.0')]
function bruttoFrei(float $netto): float
{
    return $netto * 1.19;
}

$rechner = new Preisrechner();
echo $rechner->brutto(10000) . PHP_EOL;

// Aufruf der veralteten Funktion löst einen Deprecation-Hinweis aus.
echo bruttoFrei(100.0) . PHP_EOL;
