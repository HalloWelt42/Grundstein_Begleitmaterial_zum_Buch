<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\EmailAdresse;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmailAdresseTest extends TestCase
{
    #[Test]
    public function eine_ungueltige_adresse_wird_abgewiesen(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EmailAdresse('kein-at-zeichen');
    }

    #[Test]
    public function gross_und_kleinschreibung_wird_normalisiert(): void
    {
        $gross = new EmailAdresse('Ada@Example.ORG');
        $klein = new EmailAdresse('ada@example.org');

        // Nach der Normalisierung sind beide gleich.
        self::assertTrue($gross->istGleich($klein));
        self::assertSame('ada@example.org', $gross->wert);
    }

    #[Test]
    public function umschliessende_leerzeichen_werden_entfernt(): void
    {
        self::assertSame('ada@example.org', (new EmailAdresse('  ada@example.org  '))->wert);
    }

    #[Test]
    public function die_domain_wird_korrekt_gelesen(): void
    {
        self::assertSame('example.org', (new EmailAdresse('ada@example.org'))->domain());
    }
}
