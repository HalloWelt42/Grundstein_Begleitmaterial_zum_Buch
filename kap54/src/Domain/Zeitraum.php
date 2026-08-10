<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Ein Zeitraum als Wertobjekt: zwei Zeitpunkte, ein Anfang und ein Ende.
 * Der Konstruktor stellt eine Invariante sicher, die für jeden gültigen
 * Zeitraum gilt: Das Ende liegt nie vor dem Anfang. Danach kann ein
 * Zeitraum nur noch gültig sein.
 */
final readonly class Zeitraum
{
    public function __construct(
        public DateTimeImmutable $von,
        public DateTimeImmutable $bis,
    ) {
        if ($bis < $von) {
            throw new InvalidArgumentException(
                'Das Ende eines Zeitraums darf nicht vor seinem Anfang liegen.'
            );
        }
    }

    /**
     * Liegt ein Zeitpunkt innerhalb des Zeitraums (Grenzen eingeschlossen)?
     */
    public function enthaelt(DateTimeImmutable $zeitpunkt): bool
    {
        return $zeitpunkt >= $this->von && $zeitpunkt <= $this->bis;
    }

    /**
     * Zwei Zeiträume überschneiden sich, wenn keiner vollständig vor dem
     * anderen liegt.
     */
    public function ueberschneidetSich(Zeitraum $andere): bool
    {
        return $this->von <= $andere->bis && $andere->von <= $this->bis;
    }

    public function tage(): int
    {
        return (int) $this->von->diff($this->bis)->days;
    }

    /**
     * Gleichheit über den Wert: Zwei Zeiträume sind gleich, wenn Anfang und
     * Ende denselben Zeitpunkt bezeichnen. Der Vergleich mit == prüft bei
     * DateTimeImmutable den Zeitpunkt, nicht die Objekt-Identität.
     */
    public function istGleich(Zeitraum $andere): bool
    {
        return $this->von == $andere->von && $this->bis == $andere->bis;
    }
}
