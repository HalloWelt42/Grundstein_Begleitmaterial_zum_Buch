<?php

declare(strict_types=1);

/**
 * Ein Geldbetrag, der sich selbst als Text darstellen kann. Durch
 * __toString entscheidet die Klasse, wie sie im String-Kontext aussieht.
 * Das Interface Stringable macht diese Fähigkeit im Typ sichtbar.
 */
final class Geldbetrag implements Stringable
{
    public function __construct(
        private readonly int $cent,
        private readonly string $waehrung = 'EUR',
    ) {}

    /**
     * Liefert die menschenlesbare Darstellung, etwa "12.99 EUR".
     */
    public function __toString(): string
    {
        return sprintf('%.2f %s', $this->cent / 100, $this->waehrung);
    }
}

$preis = new Geldbetrag(1299);

// 1. Direkt im String-Kontext (echo verkettet).
echo 'Einzelpreis: ' . $preis . "\n";

// 2. In der Interpolation innerhalb doppelter Anführungszeichen.
echo "Auf dem Bon steht: {$preis}\n";

// 3. Als %s in einem Formatstring.
printf("Gesamt: %s\n", $preis);

// 4. Überall dort, wo ein string erwartet wird.
$zeile = str_pad((string) $preis, 12, '.', STR_PAD_LEFT);
echo "Feld: [{$zeile}]\n";
