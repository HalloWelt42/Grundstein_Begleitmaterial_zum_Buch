<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Ein Kunde als Entity: definiert über seine Identität (die id), nicht über
 * seine Werte. Der Kunde bleibt derselbe, auch wenn er umzieht, umbenannt
 * wird oder zum Stammkunden aufsteigt. Genau darum ist er veränderlich -
 * anders als ein Wertobjekt, das man bei jeder Änderung neu erzeugt.
 */
final class Kunde
{
    public function __construct(
        private readonly int $id,
        private string $name,
        private EmailAdresse $email,
        private bool $stammkunde = false,
    ) {}

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): EmailAdresse
    {
        return $this->email;
    }

    public function istStammkunde(): bool
    {
        return $this->stammkunde;
    }

    /**
     * Eine Entity ist veränderlich: Der Kunde behält seine id, bekommt aber
     * eine neue E-Mail-Adresse. Sie ist wieder ein Wertobjekt und damit
     * garantiert gültig.
     */
    public function aendereEmail(EmailAdresse $neue): void
    {
        $this->email = $neue;
    }

    public function benenneUm(string $neuerName): void
    {
        $this->name = $neuerName;
    }

    public function zumStammkundenMachen(): void
    {
        $this->stammkunde = true;
    }

    /**
     * Identität statt Wert: Gleiche id bedeutet derselbe Kunde - selbst wenn
     * Name oder Adresse sich unterscheiden.
     */
    public function istGleich(Kunde $andere): bool
    {
        return $this->id === $andere->id;
    }
}
