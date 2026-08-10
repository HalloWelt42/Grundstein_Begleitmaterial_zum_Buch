<?php

declare(strict_types=1);

namespace App\Tests;

use App\Benutzer;
use App\BenutzerBereitsRegistriert;
use App\BenutzerRepository;
use App\InMemoryBenutzerRepository;
use App\Mailer;
use App\Registrierung;
use App\Tests\Double\FesteClock;
use App\Tests\Double\FesterTokenGenerator;
use App\Tests\Double\SpyMailer;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Der Service, geprüft mit handgeschriebenen Doubles: ein Fake fürs
 * Repository, feste Stubs für Uhr und Token, ein Spy für den Mailer.
 */
#[CoversClass(Registrierung::class)]
final class RegistrierungMitFakeTest extends TestCase
{
    #[Test]
    public function legt_einen_neuen_benutzer_mit_id_und_token_an(): void
    {
        $repository = new InMemoryBenutzerRepository();

        $benutzer = $this->registrierungMit(new SpyMailer(), $repository)
            ->registriere('ada@example.org');

        self::assertSame(1, $benutzer->id);
        self::assertSame('ABC123', $benutzer->token);
        self::assertInstanceOf(Benutzer::class, $repository->findByEmail('ada@example.org'));
    }

    #[Test]
    public function versendet_genau_eine_willkommensmail_mit_dem_token(): void
    {
        $mailer = new SpyMailer();

        $this->registrierungMit($mailer, new InMemoryBenutzerRepository())
            ->registriere('ada@example.org');

        // Der Spy hat den Versand aufgezeichnet - das lesen wir jetzt ab.
        self::assertCount(1, $mailer->versendet);
        self::assertSame('ada@example.org', $mailer->versendet[0]['an']);
        self::assertStringContainsString('ABC123', $mailer->versendet[0]['text']);
    }

    #[Test]
    public function lehnt_eine_bereits_vergebene_adresse_ab(): void
    {
        $repository = new InMemoryBenutzerRepository();
        $registrierung = $this->registrierungMit(new SpyMailer(), $repository);
        $registrierung->registriere('ada@example.org');

        $this->expectException(BenutzerBereitsRegistriert::class);
        $registrierung->registriere('ada@example.org');
    }

    /**
     * Baut den Service mit festen Stubs für Uhr und Token zusammen, sodass
     * jeder Test mit denselben bekannten Werten arbeitet.
     */
    private function registrierungMit(Mailer $mailer, BenutzerRepository $repository): Registrierung
    {
        return new Registrierung(
            $repository,
            $mailer,
            new FesteClock(new DateTimeImmutable('2026-08-10 09:00:00')),
            new FesterTokenGenerator('ABC123'),
        );
    }
}
