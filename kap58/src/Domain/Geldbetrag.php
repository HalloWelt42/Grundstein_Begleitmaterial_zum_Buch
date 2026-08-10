<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein Wertobjekt aus dem Herzen der Domäne (Kapitel 54). Es ist durch seinen
 * Wert bestimmt, unveränderlich und ab der Erzeugung gültig: Der Konstruktor
 * prüft die Werte einmal, danach muss niemand mehr nachfragen. Es hat
 * keinerlei Wissen über Datenbanken, HTTP oder Ereignisse.
 */
final readonly class Geldbetrag
{
    public function __construct(
        public int $cent,
        public string $waehrung = 'EUR',
    ) {
        // Validierung im Konstruktor - ein ungültiger Betrag entsteht gar nicht.
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

    // Bequemer Weg aus einem Euro-Betrag - rundet auf ganze Cent.
    public static function ausEuro(float $euro, string $waehrung = 'EUR'): self
    {
        return new self((int) round($euro * 100), $waehrung);
    }

    public function plus(Geldbetrag $andere): self
    {
        $this->gleicheWaehrungOderFehler($andere);

        // Unveränderlich: ein NEUES Objekt zurückgeben, das alte bleibt.
        return new self($this->cent + $andere->cent, $this->waehrung);
    }

    // Gleichheit über den Wert: gleicher Betrag und gleiche Währung.
    public function istGleich(Geldbetrag $andere): bool
    {
        return $this->cent === $andere->cent
            && $this->waehrung === $andere->waehrung;
    }

    // Anzeige-Hilfe: 4990 Cent werden zu "49,90 EUR".
    public function alsText(): string
    {
        return number_format($this->cent / 100, 2, ',', '.') . ' ' . $this->waehrung;
    }

    private function gleicheWaehrungOderFehler(Geldbetrag $andere): void
    {
        if ($this->waehrung !== $andere->waehrung) {
            throw new InvalidArgumentException(
                "Verschiedene Währungen lassen sich nicht verrechnen: "
                . "{$this->waehrung} und {$andere->waehrung}."
            );
        }
    }
}
