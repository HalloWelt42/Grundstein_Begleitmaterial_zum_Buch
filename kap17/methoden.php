<?php

declare(strict_types=1);

/**
 * Enum mit Methoden und einer Konstanten. Der ganzzahlige Wert legt
 * zugleich die Rangfolge fest: höher bedeutet dringender.
 */
enum Prioritaet: int
{
    case Niedrig  = 1;
    case Normal   = 2;
    case Hoch     = 3;
    case Kritisch = 4;

    // Konstanten sind in Enums erlaubt. Hier die Schwelle für "dringend".
    public const int SCHWELLE = 3;

    /**
     * Liefert eine sprechende Bezeichnung. match passt hier ideal: Es muss
     * jeden Fall abdecken und wirft sonst von selbst einen Fehler.
     */
    public function label(): string
    {
        return match ($this) {
            self::Niedrig  => 'Kann warten',
            self::Normal   => 'Im normalen Lauf',
            self::Hoch     => 'Bald erledigen',
            self::Kritisch => 'Sofort handeln',
        };
    }

    /**
     * Nutzt die Konstante und den hinterlegten Wert für eine Entscheidung.
     */
    public function istDringend(): bool
    {
        return $this->value >= self::SCHWELLE;
    }
}

foreach (Prioritaet::cases() as $stufe) {
    printf(
        "%-9s Wert %d  %-18s %s\n",
        $stufe->name,
        $stufe->value,
        $stufe->label(),
        $stufe->istDringend() ? 'dringend' : 'nicht dringend',
    );
}

// Methoden ruft man am einzelnen Fall auf wie bei jedem anderen Objekt.
$aktuell = Prioritaet::Hoch;
echo "\nAktuell: " . $aktuell->label() . "\n";
echo 'Schwelle: ' . Prioritaet::SCHWELLE . "\n";
