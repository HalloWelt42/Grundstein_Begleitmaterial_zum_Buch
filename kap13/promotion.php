<?php

declare(strict_types=1);

/**
 * Ohne Konstruktor-Promotion: jede Eigenschaft wird dreimal genannt -
 * als Feld deklariert, als Parameter aufgelistet und im Rumpf zugewiesen.
 */
class ArtikelKlassisch
{
    private string $name;
    private float $preis;
    private int $bestand;

    public function __construct(string $name, float $preis, int $bestand = 0)
    {
        $this->name = $name;
        $this->preis = $preis;
        $this->bestand = $bestand;
    }

    public function beschreibung(): string
    {
        return sprintf('%s: %.2f Euro (%d auf Lager)', $this->name, $this->preis, $this->bestand);
    }
}

/**
 * Mit Konstruktor-Promotion: die Sichtbarkeit vor dem Parameter macht
 * ihn zugleich zur typisierten Eigenschaft. PHP legt das Feld an und
 * weist den Wert automatisch zu. Ein Defaultwert bleibt möglich.
 */
class Artikel
{
    public function __construct(
        private string $name,
        private float $preis,
        private int $bestand = 0,
    ) {}

    public function beschreibung(): string
    {
        return sprintf('%s: %.2f Euro (%d auf Lager)', $this->name, $this->preis, $this->bestand);
    }
}

$klassisch = new ArtikelKlassisch('Schraube', 0.12, 500);
echo $klassisch->beschreibung() . "\n";

$modern = new Artikel('Mutter', 0.08, 800);
echo $modern->beschreibung() . "\n";

// Der Defaultwert greift, wenn das dritte Argument fehlt.
$ohneBestand = new Artikel('Unterlegscheibe', 0.03);
echo $ohneBestand->beschreibung() . "\n";
