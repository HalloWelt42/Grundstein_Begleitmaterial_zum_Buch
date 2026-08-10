<?php

declare(strict_types=1);

namespace App;

/**
 * Der Anwendungsdienst, um den sich das ganze Kapitel dreht. Er hängt an
 * einem Mailer und einem Logger und bekommt beide über den Konstruktor
 * hereingereicht - Dependency Injection wie in den Kapiteln 28 und 49.
 * Er baut sich nichts selbst.
 */
final class Registrierung
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly Logger $logger,
    ) {}

    /**
     * Legt ein Konto an: schreibt eine Protokollzeile und schickt eine
     * Begrüßungsmail. Beide Nebenwirkungen laufen über die injizierten
     * Abhängigkeiten, nicht über selbst gebaute Objekte.
     */
    public function registriere(string $email): string
    {
        $this->logger->notiere("Registrierung: {$email}");
        $this->mailer->sende($email, 'Willkommen');

        return "Konto für {$email} angelegt.";
    }
}
