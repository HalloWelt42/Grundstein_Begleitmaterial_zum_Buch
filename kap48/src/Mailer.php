<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Der Vertrag für den E-Mail-Versand. Der Versand ist eine Nebenwirkung
 * nach außen - im Test wollen wir ganz sicher keine echten Mails
 * verschicken. Deshalb steht auch davor ein Interface, hinter das im
 * Test ein Spy oder Mock tritt.
 */
interface Mailer
{
    public function versende(string $an, string $betreff, string $text): void;
}
