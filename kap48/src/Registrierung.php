<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Das System under Test (SUT). Der Service hängt an vier Verträgen und
 * baut keinen davon selbst - alle werden über den Konstruktor
 * hineingereicht (Dependency Injection, Kapitel 28 und Teil VIII).
 * Genau das erlaubt es, jede Abhängigkeit im Test durch ein Double zu
 * ersetzen: das Repository durch einen Fake, die Uhr und den
 * Token-Generator durch Stubs, den Mailer durch einen Spy oder Mock.
 */
final class Registrierung
{
    public function __construct(
        private readonly BenutzerRepository $benutzer,
        private readonly Mailer $mailer,
        private readonly Clock $uhr,
        private readonly TokenGenerator $token,
    ) {}

    /**
     * Registriert eine E-Mail-Adresse: prüft auf Dopplung, legt den
     * Benutzer mit festem Zeitstempel und Bestätigungscode an, speichert
     * ihn und verschickt genau eine Willkommensmail.
     */
    public function registriere(string $email): Benutzer
    {
        if ($this->benutzer->findByEmail($email) !== null) {
            throw new BenutzerBereitsRegistriert($email);
        }

        $neuer = new Benutzer(
            null,
            $email,
            $this->token->erzeuge(),
            $this->uhr->jetzt(),
        );
        $gespeichert = $this->benutzer->save($neuer);

        $this->mailer->versende(
            $email,
            'Willkommen',
            "Dein Bestätigungscode lautet: {$gespeichert->token}",
        );

        return $gespeichert;
    }
}
