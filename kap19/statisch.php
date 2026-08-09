<?php

declare(strict_types=1);

/**
 * Eine Sitzung mit fortlaufender Nummer. Der Zähler gehört zur Klasse,
 * nicht zum einzelnen Objekt: Er zählt alle jemals erzeugten Instanzen.
 */
final class Sitzung
{
    // Statische Eigenschaft: existiert genau einmal, klassenweit geteilt.
    private static int $anzahl = 0;

    // Instanz-Eigenschaft: jede Sitzung hat ihre eigene Nummer.
    public readonly int $nummer;

    public function __construct()
    {
        // Bei jeder Erzeugung den gemeinsamen Zähler erhöhen ...
        self::$anzahl++;
        // ... und den aktuellen Stand als eigene Nummer festhalten.
        $this->nummer = self::$anzahl;
    }

    /**
     * Liefert die Gesamtzahl bisher erzeugter Sitzungen.
     * Statische Methode: arbeitet ohne konkretes Objekt.
     */
    public static function anzahl(): int
    {
        return self::$anzahl;
    }
}

$a = new Sitzung();
$b = new Sitzung();
$c = new Sitzung();

// Jede Instanz kennt ihre eigene Nummer.
echo "Zweite Sitzung hat Nummer {$b->nummer}\n";

// Die Gesamtzahl fragt man an der Klasse ab, nicht an einem Objekt.
echo 'Insgesamt erzeugt: ' . Sitzung::anzahl() . "\n";
