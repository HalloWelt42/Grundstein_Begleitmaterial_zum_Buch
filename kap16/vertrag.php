<?php

declare(strict_types=1);

/**
 * Dieser Trait liefert fertigen Code, der aber auf eine Angabe angewiesen
 * ist, die er selbst nicht kennt: die Kennung des Objekts. Er verlangt sie
 * über eine abstrakte Methode - ein Vertrag an jede nutzende Klasse.
 */
trait Beschreibbar
{
    /**
     * Muss von der nutzenden Klasse geliefert werden. Der Trait schreibt
     * nur vor, dass es diese Methode gibt, nicht wie sie aussieht.
     */
    abstract public function kennung(): string;

    /**
     * Fertige Methode: baut auf der abstrakten kennung() auf, ohne den
     * konkreten Aufbau der Klasse zu kennen.
     */
    public function beschreibe(): string
    {
        return static::class . ' [' . $this->kennung() . ']';
    }
}

/**
 * Ein Produkt erfüllt den Vertrag, indem es kennung() mit einer
 * Artikelnummer füllt.
 */
final class Produkt
{
    use Beschreibbar;

    public function __construct(
        private readonly string $artikelnummer,
        private readonly string $bezeichnung,
    ) {}

    public function kennung(): string
    {
        return $this->artikelnummer;
    }
}

/**
 * Ein Benutzer erfüllt denselben Vertrag völlig anders - aus Vor- und
 * Nachname zusammengesetzt. Der Trait bleibt in beiden Fällen gleich.
 */
final class Benutzer
{
    use Beschreibbar;

    public function __construct(
        private readonly string $vorname,
        private readonly string $nachname,
    ) {}

    public function kennung(): string
    {
        return $this->vorname . ' ' . $this->nachname;
    }
}

$produkt = new Produkt('A-4711', 'Winkelschleifer');
$benutzer = new Benutzer('Ada', 'Lovelace');

echo $produkt->beschreibe() . "\n";
echo $benutzer->beschreibe() . "\n";
