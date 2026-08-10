<?php

declare(strict_types=1);

namespace App;

use NumberFormatter;

/*
 * Grundstein - Kapitel 67: Internationalisierung
 *
 * Ein Geldbetrag als unveränderliches Wertobjekt (vgl. Kapitel 54). Er
 * speichert neutral: den Betrag in der kleinsten Einheit (Cent) als int und
 * den ISO-4217-Währungscode als ASCII-Zeichenkette. Lokalisiert wird
 * ausschließlich zur Anzeige über formatiere() - der gespeicherte Zustand
 * bleibt sprachneutral und maschinenlesbar.
 */
final class Geldbetrag
{
    public function __construct(
        public readonly int $cent,
        public readonly string $waehrung,
    ) {
    }

    /**
     * Gibt den Betrag im gewünschten Sprachraum formatiert zurück - mit dem
     * dort üblichen Trennzeichen, Währungssymbol und dessen Stellung.
     */
    public function formatiere(string $locale): string
    {
        $formatierer = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // formatCurrency erwartet den Wert in ganzen Einheiten, nicht in Cent.
        return $formatierer->formatCurrency($this->cent / 100, $this->waehrung);
    }
}
