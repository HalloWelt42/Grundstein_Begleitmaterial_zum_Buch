<?php

declare(strict_types=1);

namespace App\Domain;

use DomainException;

/**
 * Ein Domänen-Service für Logik, die zu keiner einzelnen Entity gehört: Der
 * Endpreis einer Bestellung hängt sowohl von der Bestellung (ihrer Summe)
 * als auch vom Kunden (ist er Stammkunde?) ab. Weder Bestellung noch Kunde
 * wäre der natürliche Ort für diese Regel - also bekommt sie ihren eigenen,
 * zustandslosen Service. Er kennt nur Domänen-Objekte, keine Datenbank,
 * keinen HTTP-Zugriff, nichts von außen.
 */
final class Preisfindung
{
    // Stammkunden erhalten laut Rabattregel zehn Prozent Nachlass.
    private const int STAMMKUNDEN_RABATT_PROZENT = 10;

    public function endpreis(Bestellung $bestellung, Kunde $kunde): Geldbetrag
    {
        // Der Service koordiniert zwei Entities und wacht darüber, dass sie
        // zusammengehören: Die Bestellung muss dem Kunden gehören.
        if ($bestellung->kundenId() !== $kunde->id()) {
            throw new DomainException('Die Bestellung gehört nicht zu diesem Kunden.');
        }

        $summe = $bestellung->gesamtsumme();

        if (! $kunde->istStammkunde()) {
            return $summe;
        }

        $rabatt = $summe->anteil(self::STAMMKUNDEN_RABATT_PROZENT);

        return $summe->minus($rabatt);
    }
}
