<?php

declare(strict_types=1);

namespace App\Cache;

/**
 * Prüft, ob ein Cache-Schlüssel den Regeln von PSR-16 genügt.
 *
 * Der Standard verlangt, dass jede Umsetzung mindestens die Zeichen A-Z,
 * a-z, 0-9, den Unterstrich und den Punkt zulässt und Schlüssel bis zu 64
 * Zeichen speichern kann. Acht Zeichen sind dagegen für die Zukunft
 * reserviert und müssen abgelehnt werden. Ein leerer Schlüssel ist
 * ebenfalls verboten.
 */
trait PruefeSchluessel
{
    /** Die von PSR-16 reservierten, also verbotenen Zeichen. */
    private const string RESERVIERT = '{}()/\@:';

    /**
     * Wirft eine InvalidArgumentException, wenn der Schlüssel ungültig ist.
     * Andernfalls kehrt die Methode wortlos zurück.
     */
    private function pruefeSchluessel(string $schluessel): void
    {
        if ($schluessel === '') {
            throw new InvalidArgumentException('Ein Cache-Schlüssel darf nicht leer sein.');
        }

        // strpbrk() findet das erste reservierte Zeichen - oder false.
        if (strpbrk($schluessel, self::RESERVIERT) !== false) {
            throw new InvalidArgumentException(sprintf(
                'Der Schlüssel "%s" enthält ein reserviertes Zeichen aus %s.',
                $schluessel,
                self::RESERVIERT,
            ));
        }
    }
}
