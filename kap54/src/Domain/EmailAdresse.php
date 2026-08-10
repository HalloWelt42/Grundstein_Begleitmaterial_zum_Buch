<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Eine E-Mail-Adresse als Wertobjekt. Sie prüft ihre Eingabe im
 * Konstruktor und normalisiert sie zu Kleinbuchstaben - eine ungültige
 * E-Mail-Adresse kann so gar nicht erst entstehen. Wer ein EmailAdresse
 * in der Hand hält, weiß: Sie ist gültig, immer.
 */
final readonly class EmailAdresse
{
    public string $wert;

    public function __construct(string $eingabe)
    {
        $getrimmt = trim($eingabe);

        // Validierung im Konstruktor mit der eingebauten Prüfung. Schlägt sie
        // fehl, entsteht kein halbfertiges Objekt, sondern eine Ausnahme.
        if (filter_var($getrimmt, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException(
                "Keine gültige E-Mail-Adresse: '{$eingabe}'."
            );
        }

        // Normalisierung: klein geschrieben gespeichert, damit
        // 'Ada@Example.ORG' und 'ada@example.org' als gleich gelten.
        $this->wert = mb_strtolower($getrimmt);
    }

    /**
     * Der Teil hinter dem @-Zeichen. Die Validierung stellt sicher, dass es
     * genau ein solches Zeichen gibt.
     */
    public function domain(): string
    {
        return substr($this->wert, strpos($this->wert, '@') + 1);
    }

    public function istGleich(EmailAdresse $andere): bool
    {
        return $this->wert === $andere->wert;
    }

    public function __toString(): string
    {
        return $this->wert;
    }
}
