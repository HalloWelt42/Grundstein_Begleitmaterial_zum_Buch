<?php

declare(strict_types=1);

namespace App\Application;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Ein Eingabeobjekt (DTO) an der Grenze zur Anwendungsschicht. Es trägt
 * die rohe, noch ungeprüfte Absicht "jemand möchte sich mit dieser
 * Adresse anmelden" von außen herein. Der Controller baut es aus der
 * HTTP-Eingabe; der Anwendungsdienst nimmt nur noch dieses klar getippte
 * Objekt entgegen und muss nie in einem $_POST-Array stochern.
 */
final class Anmeldebefehl
{
    public function __construct(
        public readonly string $email,
    ) {}
}
