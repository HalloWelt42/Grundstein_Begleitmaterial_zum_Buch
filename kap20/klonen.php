<?php

declare(strict_types=1);

/**
 * Eine kleine Wertklasse, die als Eigenschaft in einer anderen steckt.
 */
final class Adresse
{
    public function __construct(
        public string $stadt,
    ) {}
}

/**
 * Beim Kopieren mit clone erzeugt PHP eine FLACHE Kopie: eingebettete
 * Objekte werden nicht mitkopiert, sondern nur ihre Referenz. __clone
 * greift danach ein und stellt eine echte, tiefe Kopie her.
 */
final class Person
{
    public function __construct(
        public string $name,
        public Adresse $adresse,
    ) {}

    /**
     * Läuft automatisch NACH dem Kopieren des Objekts. Ohne diese Zeile
     * teilten sich Original und Kopie dieselbe Adresse.
     */
    public function __clone(): void
    {
        $this->adresse = clone $this->adresse;
    }
}

$original = new Person('Ada', new Adresse('London'));
$kopie = clone $original;

$kopie->name = 'Grace';
$kopie->adresse->stadt = 'Baltimore';

echo "{$original->name} wohnt in {$original->adresse->stadt}\n";
echo "{$kopie->name} wohnt in {$kopie->adresse->stadt}\n";
