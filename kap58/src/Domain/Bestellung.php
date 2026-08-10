<?php

declare(strict_types=1);

namespace App\Domain;

use DomainException;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Die Entity im Zentrum der Domäne (Kapitel 54). Sie ist durch ihre
 * Identität (die id) bestimmt, nicht durch ihren Wert. Der Konstruktor
 * wacht über eine Invariante: Eine Bestellung ohne echten Betrag ergibt
 * keinen Sinn. Zustandswechsel laufen über benannte Methoden, die die
 * Fachregeln durchsetzen. Kein PDO, kein HTTP - reine Fachlichkeit.
 */
final class Bestellung
{
    public function __construct(
        public readonly ?int $id,
        public readonly EmailAdresse $kunde,
        public readonly Geldbetrag $betrag,
        public readonly Bestellstatus $status = Bestellstatus::Neu,
    ) {
        // Invariante: Der Betrag einer Bestellung muss größer als null sein.
        // Der Geldbetrag selbst lässt null zu; die Bestellung tut es nicht.
        if ($betrag->cent <= 0) {
            throw new DomainException(
                'Eine Bestellung braucht einen Betrag größer als null.'
            );
        }
    }

    // Benannter Konstruktor: eine frische Bestellung hat noch keine id -
    // die vergibt erst der Persistenz-Adapter beim Speichern.
    public static function neu(EmailAdresse $kunde, Geldbetrag $betrag): self
    {
        return new self(null, $kunde, $betrag, Bestellstatus::Neu);
    }

    // Fachregel: Eine bereits bezahlte Bestellung wird nicht erneut bezahlt.
    // Diese Entscheidung gehört in die Domäne, nicht in einen Adapter.
    public function bezahle(): self
    {
        if ($this->status === Bestellstatus::Bezahlt) {
            throw new DomainException('Die Bestellung ist bereits bezahlt.');
        }

        // Unveränderlicher Zustandswechsel: ein neues Objekt mit gleicher id.
        return new self($this->id, $this->kunde, $this->betrag, Bestellstatus::Bezahlt);
    }

    public function istBezahlt(): bool
    {
        return $this->status === Bestellstatus::Bezahlt;
    }

    // Identität statt Wert: gleiche id bedeutet dieselbe Bestellung.
    public function istGleich(Bestellung $andere): bool
    {
        return $this->id !== null && $this->id === $andere->id;
    }
}
