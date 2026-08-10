<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Wird geworfen, wenn sich eine bereits bekannte E-Mail-Adresse ein
 * zweites Mal registrieren will. Eine erwartbare Laufzeit-Situation,
 * also eine RuntimeException (siehe Kapitel 26).
 */
final class BenutzerBereitsRegistriert extends RuntimeException
{
    public function __construct(public readonly string $email)
    {
        parent::__construct("Bereits registriert: {$email}");
    }
}
