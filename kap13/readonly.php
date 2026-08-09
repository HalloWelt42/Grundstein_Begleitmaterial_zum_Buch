<?php

declare(strict_types=1);

/**
 * Ein Wertobjekt für einen Geldbetrag. readonly-Eigenschaften werden
 * einmal im Konstruktor gesetzt und sind danach unveränderlich. Wer einen
 * anderen Betrag braucht, erzeugt ein neues Objekt.
 */
final class Geld
{
    public function __construct(
        public readonly int $cent,
        public readonly string $waehrung = 'EUR',
    ) {}

    /**
     * Liefert ein NEUES Geld-Objekt mit erhöhtem Betrag, statt das
     * bestehende zu verändern. So bleibt das Original unberührt.
     */
    public function plus(Geld $anderer): Geld
    {
        return new Geld($this->cent + $anderer->cent, $this->waehrung);
    }

    public function alsText(): string
    {
        return sprintf('%.2f %s', $this->cent / 100, $this->waehrung);
    }
}

$preis = new Geld(1250);
$versand = new Geld(499);
$summe = $preis->plus($versand);

echo 'Preis:   ' . $preis->alsText() . "\n";
echo 'Versand: ' . $versand->alsText() . "\n";
echo 'Summe:   ' . $summe->alsText() . "\n";

// Der Versuch, ein readonly-Feld nachträglich zu ändern, scheitert.
try {
    $preis->cent = 9999;
} catch (Error $e) {
    echo 'Änderung verweigert: ' . $e->getMessage() . "\n";
}
