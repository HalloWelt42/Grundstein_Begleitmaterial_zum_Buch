<?php

declare(strict_types=1);

namespace App\Domain;

use LogicException;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Die Bestellung ist eine Entity: Sie hat eine Identität (die id), die über
 * die Zeit gleich bleibt, auch wenn sich ihr Zustand ändert. Sie lebt im
 * Zentrum des Sechsecks und kennt weder PDO noch HTTP noch einen konkreten
 * Zahlungsanbieter - nur ihre eigene Fachlogik. Weil sie unveränderlich ist,
 * liefert jede Zustandsänderung ein neues Objekt zurück.
 */
final class Bestellung
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $kunde,
        public readonly Geld $betrag,
        public readonly bool $bezahlt = false,
    ) {}

    // Eine frische Bestellung hat noch keine id - die vergibt erst der
    // Persistenz-Adapter beim Speichern.
    public static function neu(string $kunde, Geld $betrag): self
    {
        return new self(null, $kunde, $betrag, false);
    }

    // Fachregel: Eine bereits bezahlte Bestellung darf nicht erneut bezahlt
    // werden. Diese Entscheidung gehört in die Domäne, nicht in einen Adapter.
    public function alsBezahltMarkiert(): self
    {
        if ($this->bezahlt) {
            throw new LogicException('Die Bestellung ist bereits bezahlt.');
        }

        return new self($this->id, $this->kunde, $this->betrag, true);
    }

    public function istOffen(): bool
    {
        return $this->bezahlt === false;
    }
}
