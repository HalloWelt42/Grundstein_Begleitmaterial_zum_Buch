<?php

declare(strict_types=1);

/**
 * Ein Rechteck mit Breite und Höhe. Die Klasse ist der Bauplan; jedes
 * konkrete Rechteck ist ein Objekt (eine Instanz) dieses Bauplans.
 */
final class Rechteck
{
    // Zwei typisierte Eigenschaften. Jedes Objekt speichert eigene Werte.
    public float $breite;
    public float $hoehe;

    /**
     * Berechnet den Flächeninhalt. Über $this greift die Methode auf die
     * Eigenschaften genau dieses Objekts zu.
     */
    public function flaeche(): float
    {
        return $this->breite * $this->hoehe;
    }

    /**
     * Berechnet den Umfang des Rechtecks.
     */
    public function umfang(): float
    {
        return 2 * ($this->breite + $this->hoehe);
    }
}

// Ein Objekt aus dem Bauplan erzeugen und seine Eigenschaften setzen.
$r = new Rechteck();
$r->breite = 4.0;
$r->hoehe = 2.5;

echo 'Fläche: ' . $r->flaeche() . "\n";
echo 'Umfang: ' . $r->umfang() . "\n";
