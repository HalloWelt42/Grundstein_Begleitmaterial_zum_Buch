<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Ein Geldbetrag als Wertobjekt (Value Object): definiert allein durch
 * seinen Wert - den Betrag in Cent und die Währung. Ein Geldbetrag ist
 * unveränderlich (readonly) und nach der Erzeugung immer gültig, weil der
 * Konstruktor jede ungültige Eingabe sofort zurückweist. Zwei Geldbeträge
 * mit gleichem Cent-Wert und gleicher Währung gelten als gleich; eine
 * eigene Identität besitzen sie nicht.
 */
final readonly class Geldbetrag
{
    public function __construct(
        public int $cent,
        public string $waehrung = 'EUR',
    ) {
        // Validierung im Konstruktor: Danach ist der Betrag garantiert gültig,
        // und kein Aufrufer muss ihn je erneut prüfen.
        if ($cent < 0) {
            throw new InvalidArgumentException(
                "Ein Geldbetrag darf nicht negativ sein, {$cent} Cent gegeben."
            );
        }

        // Eine Währung ist ein dreistelliger Code aus Großbuchstaben (ISO 4217).
        if (preg_match('/^[A-Z]{3}$/', $waehrung) !== 1) {
            throw new InvalidArgumentException(
                "Ungültiger Währungscode: '{$waehrung}'."
            );
        }
    }

    /**
     * Benannter Konstruktor: liest sich am Aufrufort wie Fachsprache,
     * etwa Geldbetrag::inEuro(19, 99) für 19,99 EUR.
     */
    public static function inEuro(int $euro, int $cent = 0): self
    {
        return new self($euro * 100 + $cent, 'EUR');
    }

    public function plus(Geldbetrag $andere): self
    {
        $this->gleicheWaehrungOderFehler($andere);

        // Unveränderlich: Wir geben ein NEUES Objekt zurück, statt dieses zu ändern.
        return new self($this->cent + $andere->cent, $this->waehrung);
    }

    public function minus(Geldbetrag $andere): self
    {
        $this->gleicheWaehrungOderFehler($andere);

        // Der Konstruktor wacht darüber, dass das Ergebnis nicht negativ wird.
        return new self($this->cent - $andere->cent, $this->waehrung);
    }

    public function mal(int $faktor): self
    {
        if ($faktor < 0) {
            throw new InvalidArgumentException('Der Faktor darf nicht negativ sein.');
        }

        return new self($this->cent * $faktor, $this->waehrung);
    }

    /**
     * Liefert einen prozentualen Anteil, ganzzahlig gerundet - so entstehen
     * keine Cent-Bruchteile.
     */
    public function anteil(int $prozent): self
    {
        if ($prozent < 0) {
            throw new InvalidArgumentException('Ein Prozentsatz darf nicht negativ sein.');
        }

        return new self(intdiv($this->cent * $prozent, 100), $this->waehrung);
    }

    /**
     * Gleichheit über den Wert: gleicher Betrag und gleiche Währung bedeuten
     * denselben Geldbetrag.
     */
    public function istGleich(Geldbetrag $andere): bool
    {
        return $this->cent === $andere->cent
            && $this->waehrung === $andere->waehrung;
    }

    /**
     * Kleine Anzeige-Hilfe für die Ausgabe. Sie formatiert nur und trifft
     * keine fachliche Entscheidung.
     */
    public function alsText(): string
    {
        return number_format($this->cent / 100, 2, ',', '.') . ' ' . $this->waehrung;
    }

    private function gleicheWaehrungOderFehler(Geldbetrag $andere): void
    {
        if ($this->waehrung !== $andere->waehrung) {
            throw new InvalidArgumentException(
                "Währungen passen nicht zusammen: {$this->waehrung} und {$andere->waehrung}."
            );
        }
    }
}
