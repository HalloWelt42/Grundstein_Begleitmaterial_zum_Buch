<?php

declare(strict_types=1);

namespace App;

/**
 * Ein Mailer, der nicht wirklich verschickt, sondern jeden Versand nur
 * protokolliert. Wichtig für dieses Kapitel: Er braucht dafür einen
 * Logger und bekommt ihn über den Konstruktor - eine Abhängigkeit, die
 * selbst wieder eine Abhängigkeit hat.
 */
final class ProtokollMailer implements Mailer
{
    public function __construct(
        private readonly Logger $logger,
    ) {}

    public function sende(string $an, string $betreff): void
    {
        $this->logger->notiere("Mail an {$an}: {$betreff}");
    }
}
