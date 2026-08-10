<?php

declare(strict_types=1);

namespace App;

use Collator;

/*
 * Grundstein - Kapitel 67: Internationalisierung
 *
 * Ein Katalog hält Bezeichnungen sprachneutral (UTF-8, in Einfügereihenfolge)
 * und gibt sie erst auf Wunsch sprachrichtig sortiert heraus. Das Sortieren
 * übernimmt der Collator; ein roher Byte-Vergleich würde Umlaute falsch
 * einordnen. Der Katalog selbst bleibt dabei unverändert.
 */
final class Katalog
{
    /** @param list<string> $namen */
    public function __construct(
        private readonly array $namen,
    ) {
    }

    /**
     * Liefert die Namen im gewünschten Sprachraum sortiert, ohne den Katalog
     * zu verändern - das Original bleibt in seiner Einfügereihenfolge.
     *
     * @return list<string>
     */
    public function sortiertNach(string $locale): array
    {
        $collator = new Collator($locale);

        // Auf einer Kopie sortieren: $this->namen bleibt unberührt.
        $kopie = $this->namen;
        $collator->sort($kopie);

        return $kopie;
    }
}
