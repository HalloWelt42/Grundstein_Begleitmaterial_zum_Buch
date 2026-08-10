<?php

declare(strict_types=1);

namespace App\Tests;

use App\Benutzer;
use App\BenutzerBereitsRegistriert;
use App\BenutzerRepository;
use App\Clock;
use App\Mailer;
use App\Registrierung;
use App\TokenGenerator;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Derselbe Service, ein zweites Mal geprüft - diesmal mit den Bordmitteln
 * von PHPUnit: createStub() für die reinen Werte-Lieferanten und
 * createMock() für die eine Interaktion, die wirklich zählt.
 */
#[CoversClass(Registrierung::class)]
final class RegistrierungMitMockTest extends TestCase
{
    #[Test]
    public function versendet_genau_eine_willkommensmail_mit_dem_token(): void
    {
        // Stubs: liefern feste Antworten und prüfen nichts.
        $repository = $this->createStub(BenutzerRepository::class);
        $repository->method('findByEmail')->willReturn(null);
        $repository->method('save')->willReturnArgument(0);

        $uhr = $this->createStub(Clock::class);
        $uhr->method('jetzt')->willReturn(new DateTimeImmutable('2026-08-10 09:00:00'));

        $token = $this->createStub(TokenGenerator::class);
        $token->method('erzeuge')->willReturn('ABC123');

        // Mock: die Erwartung wird vorab formuliert und am Ende geprüft.
        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->once())
            ->method('versende')
            ->with(
                'ada@example.org',
                'Willkommen',
                $this->stringContains('ABC123'),
            );

        $registrierung = new Registrierung($repository, $mailer, $uhr, $token);

        $benutzer = $registrierung->registriere('ada@example.org');

        self::assertSame('ABC123', $benutzer->token);
    }

    #[Test]
    public function versendet_keine_mail_wenn_die_adresse_schon_existiert(): void
    {
        $repository = $this->createStub(BenutzerRepository::class);
        $repository->method('findByEmail')->willReturn(
            new Benutzer(1, 'ada@example.org', 'X', new DateTimeImmutable()),
        );

        // Erwartung an den Mock: versende() darf NIE aufgerufen werden.
        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->never())->method('versende');

        // Uhr und Token werden hier nie gebraucht - reine Dummys genügen.
        $registrierung = new Registrierung(
            $repository,
            $mailer,
            $this->createStub(Clock::class),
            $this->createStub(TokenGenerator::class),
        );

        $this->expectException(BenutzerBereitsRegistriert::class);
        $registrierung->registriere('ada@example.org');
    }
}
