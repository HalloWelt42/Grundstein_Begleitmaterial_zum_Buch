<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 15: Vererbung, abstrakte Klassen und Interfaces
 *
 * Teil 1: Vererbung mit extends. Eine Unterklasse erbt Eigenschaften und
 * Methoden der Oberklasse, ergänzt Eigenes und darf geerbte Methoden
 * überschreiben. Mit parent:: ruft die Unterklasse die ursprüngliche
 * Fassung der Oberklasse auf, statt sie doppelt zu schreiben.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

// --- Die Oberklasse: gemeinsames Verhalten -----------------------------

/**
 * Ein Fahrzeug mit Marke und aktueller Geschwindigkeit. Diese Klasse
 * beschreibt, was alle Fahrzeuge gemeinsam haben.
 */
class Fahrzeug
{
    public function __construct(
        protected readonly string $marke,
        protected int $tempo = 0,
    ) {}

    /**
     * Erhöht die Geschwindigkeit um den angegebenen Wert.
     */
    public function beschleunige(int $delta): void
    {
        $this->tempo += $delta;
    }

    /**
     * Liefert eine kurze Beschreibung des aktuellen Zustands.
     */
    public function info(): string
    {
        return "{$this->marke} fährt {$this->tempo} km/h";
    }
}

// --- Die Unterklasse: erbt und ergänzt ---------------------------------

/**
 * Ein Elektroauto ist ein Fahrzeug mit zusätzlicher Reichweite. Es erbt
 * beschleunige() unverändert und überschreibt info(), um die Reichweite
 * anzuhängen.
 */
class Elektroauto extends Fahrzeug
{
    public function __construct(
        string $marke,
        private int $reichweite,
        int $tempo = 0,
    ) {
        // Den geerbten Teil richtet der Konstruktor der Oberklasse ein.
        parent::__construct($marke, $tempo);
    }

    /**
     * Überschreibt info() und baut auf der Fassung der Oberklasse auf,
     * statt den Text noch einmal von Hand zusammenzusetzen.
     */
    public function info(): string
    {
        return parent::info() . ", {$this->reichweite} km Reichweite";
    }
}

$fahrzeug = new Fahrzeug('Lastwagen');
$fahrzeug->beschleunige(50);
echo $fahrzeug->info() . "\n";

$auto = new Elektroauto('Kleinwagen', reichweite: 320);
$auto->beschleunige(30);  // geerbte Methode, unverändert
$auto->beschleunige(15);
echo $auto->info() . "\n";  // überschriebene Methode

// Eine Instanz der Unterklasse ist zugleich eine Instanz der Oberklasse.
var_dump($auto instanceof Fahrzeug);
