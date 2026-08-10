<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\AnmeldeService;
use App\Application\Anmeldebefehl;
use App\Application\BereitsAngemeldet;
use App\Application\UngueltigeEmail;
use App\Tests\Double\FesteClock;
use App\Tests\Double\InMemoryAbonnentRepository;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten
 *
 * Der Anwendungsdienst wird mit festgenagelten Abhängigkeiten geprüft:
 * ein Speicher-Repository statt einer echten Datenbank und eine feste Uhr
 * statt der Systemuhr. Beide kommen über den Konstruktor herein - genau
 * das macht die Fachlogik ohne Infrastruktur prüfbar.
 */
#[CoversClass(AnmeldeService::class)]
final class AnmeldeServiceTest extends TestCase
{
    private function dienstMit(InMemoryAbonnentRepository $repo): AnmeldeService
    {
        return new AnmeldeService(
            $repo,
            new FesteClock(new DateTimeImmutable('2026-08-10 09:00:00')),
        );
    }

    #[Test]
    public function meldet_eine_neue_adresse_an(): void
    {
        $repo = new InMemoryAbonnentRepository();

        $abonnent = $this->dienstMit($repo)->meldeAn(
            new Anmeldebefehl('Ada@Example.org'),
        );

        self::assertSame(1, $abonnent->id);
        // Die Adresse wird vereinheitlicht: klein geschrieben.
        self::assertSame('ada@example.org', $abonnent->email);
        self::assertSame(
            '2026-08-10 09:00:00',
            $abonnent->angemeldetAm->format('Y-m-d H:i:s'),
        );
        self::assertCount(1, $repo->alle());
    }

    #[Test]
    public function weist_eine_doppelte_anmeldung_ab(): void
    {
        $repo    = new InMemoryAbonnentRepository();
        $dienst  = $this->dienstMit($repo);
        $dienst->meldeAn(new Anmeldebefehl('ada@example.org'));

        $this->expectException(BereitsAngemeldet::class);
        $dienst->meldeAn(new Anmeldebefehl('ada@example.org'));
    }

    #[Test]
    public function weist_eine_ungueltige_adresse_ab(): void
    {
        $this->expectException(UngueltigeEmail::class);

        $this->dienstMit(new InMemoryAbonnentRepository())
            ->meldeAn(new Anmeldebefehl('kein-at-zeichen'));
    }
}
