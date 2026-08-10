<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein zweites Wertobjekt (Kapitel 54). Statt eine E-Mail-Adresse als nackten
 * string durch die Anwendung zu reichen, kapseln wir sie: Der Konstruktor
 * prüft die Gültigkeit und normalisiert die Schreibweise. Wer eine
 * EmailAdresse in der Hand hält, hält garantiert eine gültige.
 */
final readonly class EmailAdresse
{
    public string $wert;

    public function __construct(string $eingabe)
    {
        $getrimmt = trim($eingabe);

        if (filter_var($getrimmt, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException(
                "Keine gültige E-Mail-Adresse: '{$eingabe}'."
            );
        }

        // Normalisierung: klein geschrieben, damit 'Ada@Example.ORG' und
        // 'ada@example.org' als dieselbe Adresse gelten.
        $this->wert = mb_strtolower($getrimmt);
    }

    public function istGleich(EmailAdresse $andere): bool
    {
        return $this->wert === $andere->wert;
    }
}
