<?php

declare(strict_types=1);

namespace App\Domain;

use DomainException;

/**
 * Eine Bestellung als Entity und zugleich Wurzel eines Aggregats. Sie hat
 * eine Identität (die id), die über ihre ganze Lebenszeit gleich bleibt,
 * und einen veränderlichen Zustand: Posten kommen hinzu, der Status wandert
 * von offen über bezahlt oder storniert. Als Aggregatwurzel wacht sie
 * selbst über ihre Invarianten - Regeln, die immer gelten müssen. Von außen
 * geht jede Änderung nur durch ihre Methoden, nie direkt an die Posten.
 */
final class Bestellung
{
    /** @var list<Bestellposten> */
    private array $posten = [];

    private Bestellstatus $status = Bestellstatus::Offen;

    public function __construct(
        private readonly int $id,
        private readonly int $kundenId,
        private readonly string $waehrung = 'EUR',
    ) {}

    public function id(): int
    {
        return $this->id;
    }

    public function kundenId(): int
    {
        return $this->kundenId;
    }

    public function status(): Bestellstatus
    {
        return $this->status;
    }

    /** @return list<Bestellposten> */
    public function posten(): array
    {
        return $this->posten;
    }

    /**
     * Ein Posten kommt nur zu einer offenen Bestellung hinzu, und nur in der
     * Währung der Bestellung. Diese beiden Invarianten schützt das Aggregat
     * selbst - niemand kann sie von außen umgehen.
     */
    public function fuegeHinzu(Bestellposten $posten): void
    {
        if ($this->status !== Bestellstatus::Offen) {
            throw new DomainException(
                'Zu einer nicht mehr offenen Bestellung kann kein Posten hinzukommen.'
            );
        }

        if ($posten->einzelpreis->waehrung !== $this->waehrung) {
            throw new DomainException(
                'Der Posten hat eine andere Währung als die Bestellung.'
            );
        }

        $this->posten[] = $posten;
    }

    /**
     * Die Summe aller Posten als Geldbetrag. Sie wird bei Bedarf berechnet,
     * nicht gespeichert - so kann sie nie veralten.
     */
    public function gesamtsumme(): Geldbetrag
    {
        $summe = new Geldbetrag(0, $this->waehrung);
        foreach ($this->posten as $posten) {
            $summe = $summe->plus($posten->zwischensumme());
        }

        return $summe;
    }

    /**
     * Invariante: Eine leere oder bereits stornierte Bestellung lässt sich
     * nicht bezahlen.
     */
    public function bezahle(): void
    {
        if ($this->posten === []) {
            throw new DomainException('Eine leere Bestellung kann nicht bezahlt werden.');
        }

        if ($this->status === Bestellstatus::Storniert) {
            throw new DomainException('Eine stornierte Bestellung kann nicht bezahlt werden.');
        }

        $this->status = Bestellstatus::Bezahlt;
    }

    /**
     * Invariante: Was schon bezahlt ist, wird nicht mehr storniert - dafür
     * gäbe es fachlich eine Rückerstattung, keinen stillen Statuswechsel.
     */
    public function storniere(): void
    {
        if ($this->status === Bestellstatus::Bezahlt) {
            throw new DomainException('Eine bezahlte Bestellung kann nicht storniert werden.');
        }

        $this->status = Bestellstatus::Storniert;
    }

    /**
     * Gleichheit über die Identität: Zwei Bestellungen sind dieselbe, wenn
     * ihre id gleich ist - unabhängig von Posten oder Status. Das ist der
     * Unterschied zu einem Wertobjekt, das über seine Werte gleich ist.
     */
    public function istGleich(Bestellung $andere): bool
    {
        return $this->id === $andere->id;
    }
}
