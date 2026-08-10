<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\AnmeldeService;
use App\Http\AnmeldeController;
use App\Tests\Double\FesteClock;
use App\Tests\Double\InMemoryAbonnentRepository;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten
 *
 * Die Präsentationsschicht lässt sich ohne Webserver prüfen. Wir
 * verdrahten den Controller mit einem Dienst auf Speicher-Repository und
 * fester Uhr und prüfen nur das, wofür der Controller zuständig ist:
 * dass er Ergebnisse und Fehler auf die richtigen HTTP-Statuscodes
 * abbildet. Die Fachlogik selbst prüft der AnmeldeServiceTest.
 */
#[CoversClass(AnmeldeController::class)]
final class AnmeldeControllerTest extends TestCase
{
    private function controller(): AnmeldeController
    {
        $service = new AnmeldeService(
            new InMemoryAbonnentRepository(),
            new FesteClock(new DateTimeImmutable('2026-08-10 09:00:00')),
        );

        return new AnmeldeController($service);
    }

    #[Test]
    public function antwortet_mit_201_bei_erfolg(): void
    {
        $antwort = $this->controller()->anmelden(['email' => 'ada@example.org']);

        self::assertSame(201, $antwort->status);
        self::assertStringContainsString('ada@example.org', $antwort->rumpf);
    }

    #[Test]
    public function antwortet_mit_409_bei_doppelter_anmeldung(): void
    {
        $controller = $this->controller();
        $controller->anmelden(['email' => 'ada@example.org']);

        $antwort = $controller->anmelden(['email' => 'ada@example.org']);

        self::assertSame(409, $antwort->status);
    }

    #[Test]
    public function antwortet_mit_422_bei_ungueltiger_adresse(): void
    {
        $antwort = $this->controller()->anmelden(['email' => 'kaputt']);

        self::assertSame(422, $antwort->status);
    }

    #[Test]
    public function antwortet_mit_422_bei_fehlendem_feld(): void
    {
        $antwort = $this->controller()->anmelden([]);

        self::assertSame(422, $antwort->status);
    }
}
