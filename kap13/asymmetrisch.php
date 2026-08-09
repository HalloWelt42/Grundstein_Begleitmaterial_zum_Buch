<?php

declare(strict_types=1);

/**
 * Asymmetrische Sichtbarkeit (PHP 8.4): public private(set) macht ein
 * Feld von außen LESBAR, aber nur INNERHALB der Klasse schreibbar. So
 * entfällt der Boilerplate eines reinen Getters, ohne die Kapselung
 * aufzugeben.
 */
final class Warenkorb
{
    // Von außen lesbar, nur intern beschreibbar.
    public private(set) int $anzahl = 0;

    public private(set) float $gesamt = 0.0;

    /**
     * Legt einen Posten in den Korb. Nur diese Methode darf die
     * geschützten Felder fortschreiben.
     */
    public function hinzufuegen(float $preis): void
    {
        $this->anzahl += 1;
        $this->gesamt += $preis;
    }
}

$korb = new Warenkorb();
$korb->hinzufuegen(2.50);
$korb->hinzufuegen(4.90);

// Lesen von außen ist erlaubt - ganz ohne getAnzahl() oder getGesamt().
printf("Posten: %d, Gesamt: %.2f Euro\n", $korb->anzahl, $korb->gesamt);

// Schreiben von außen ist verboten und wird abgefangen.
try {
    $korb->gesamt = 0.0;
} catch (Error $e) {
    echo 'Schreibzugriff verweigert: ' . $e->getMessage() . "\n";
}
