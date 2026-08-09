<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 15: Vererbung, abstrakte Klassen und Interfaces
 *
 * Teil 2: Abstrakte Klassen. Eine abstrakte Klasse ist ein Bauplan mit
 * Lücken: Sie kann fertige Methoden mitbringen, lässt aber einzelne
 * Methoden offen (abstract) und zwingt jede Unterklasse, sie zu füllen.
 * Von einer abstrakten Klasse selbst kann man kein Objekt erzeugen.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

// --- Der Bauplan mit Lücken --------------------------------------------

/**
 * Eine geometrische Form. Wie sich Fläche und Umfang berechnen, weiß
 * erst die konkrete Form - deshalb bleiben diese Methoden abstrakt. Die
 * Beschreibung dagegen ist für alle Formen gleich und schon fertig.
 */
abstract class Form
{
    /**
     * Liefert die Fläche. Jede Unterklasse muss diese Methode füllen.
     */
    abstract public function flaeche(): float;

    /**
     * Liefert den Umfang. Ebenfalls von der Unterklasse zu füllen.
     */
    abstract public function umfang(): float;

    /**
     * Fertige Methode: nutzt die abstrakten Methoden, ohne sie zu kennen.
     * Sie funktioniert für jede künftige Form automatisch mit.
     */
    public function beschreibung(): string
    {
        return sprintf(
            '%s: Fläche %.2f, Umfang %.2f',
            static::class,
            $this->flaeche(),
            $this->umfang(),
        );
    }
}

// --- Konkrete Formen füllen die Lücken ---------------------------------

/**
 * Ein Kreis. Er füllt beide abstrakten Methoden mit seiner Formel.
 */
class Kreis extends Form
{
    public function __construct(
        private readonly float $radius,
    ) {}

    public function flaeche(): float
    {
        return M_PI * $this->radius ** 2;
    }

    public function umfang(): float
    {
        return 2 * M_PI * $this->radius;
    }
}

/**
 * Ein Rechteck mit Breite und Höhe.
 */
class Rechteck extends Form
{
    public function __construct(
        private readonly float $breite,
        private readonly float $hoehe,
    ) {}

    public function flaeche(): float
    {
        return $this->breite * $this->hoehe;
    }

    public function umfang(): float
    {
        return 2 * ($this->breite + $this->hoehe);
    }
}

// Eine Liste verschiedener Formen - alle vom gemeinsamen Typ Form.
$formen = [
    new Kreis(2.0),
    new Rechteck(3.0, 4.0),
];

foreach ($formen as $form) {
    // beschreibung() steckt in der abstrakten Oberklasse und ruft
    // intern die konkrete flaeche()/umfang() der jeweiligen Form auf.
    echo $form->beschreibung() . "\n";
}
