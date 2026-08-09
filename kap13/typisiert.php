<?php

declare(strict_types=1);

/**
 * Typisierte Eigenschaften mit Defaultwerten. Jedes Feld trägt seinen Typ
 * direkt bei der Deklaration; der Default legt den Startwert fest.
 */
final class Spielfigur
{
    // Mit Defaultwert: das Feld ist von Anfang an initialisiert.
    public int $lebenspunkte = 100;

    public string $name = 'Namenlos';

    // Ohne Defaultwert und ohne Nullbarkeit: die Eigenschaft gilt als
    // "uninitialisiert", bis ihr zum ersten Mal ein Wert zugewiesen wird.
    public int $punkte;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->punkte = 0;
    }
}

$figur = new Spielfigur('Ada');

// Die Felder mit Default sind sofort belegt.
printf("%s startet mit %d Lebenspunkten und %d Punkten.\n",
    $figur->name, $figur->lebenspunkte, $figur->punkte);

/**
 * Was passiert bei einer typisierten Eigenschaft OHNE Default, auf die
 * zugegriffen wird, bevor sie gesetzt wurde? PHP meldet einen Fehler,
 * statt still null zu liefern.
 */
final class Messung
{
    public float $wert; // typisiert, aber ohne Defaultwert
}

$messung = new Messung();
try {
    echo $messung->wert;
} catch (Error $e) {
    echo 'Fehler: ' . $e->getMessage() . "\n";
}
