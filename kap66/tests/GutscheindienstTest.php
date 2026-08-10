<?php

declare(strict_types=1);

namespace App\Tests;

use App\FesteClock;
use App\Gutscheindienst;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Prüft den Gutscheindienst gegen eine feste Uhr. Weil die Zeit über den
 * PSR-20-Vertrag hereinkommt, sind Ausstellungs- und Ablaufzeitpunkt im
 * Voraus bekannt und exakt prüfbar - ohne flackernde, von der Systemuhr
 * abhängige Tests.
 */
#[CoversClass(Gutscheindienst::class)]
final class GutscheindienstTest extends TestCase
{
    private const string UTC = 'UTC';

    #[Test]
    public function stellt_gutschein_mit_festem_ablauf_dreissig_tage_spaeter_aus(): void
    {
        $uhr = new FesteClock(
            new DateTimeImmutable('2026-08-10 09:00:00', new DateTimeZone(self::UTC)),
        );
        $dienst = new Gutscheindienst($uhr);

        $gutschein = $dienst->stelleAus(2500);

        self::assertSame(2500, $gutschein->wertCent);
        self::assertSame(
            '2026-08-10 09:00:00',
            $gutschein->ausgestelltAm->format('Y-m-d H:i:s'),
        );
        // Genau 30 Tage später - nur prüfbar, weil die Uhr fest steht.
        self::assertSame(
            '2026-09-09 09:00:00',
            $gutschein->gueltigBis->format('Y-m-d H:i:s'),
        );
    }

    #[Test]
    public function der_gutschein_ist_am_ausstellungstag_gueltig_und_nach_ablauf_nicht(): void
    {
        $zone = new DateTimeZone(self::UTC);
        $uhr = new FesteClock(new DateTimeImmutable('2026-08-10 09:00:00', $zone));
        $dienst = new Gutscheindienst($uhr);

        $gutschein = $dienst->stelleAus(1000);

        self::assertTrue(
            $gutschein->istGueltigAm(new DateTimeImmutable('2026-08-10 09:00:00', $zone)),
        );
        self::assertFalse(
            $gutschein->istGueltigAm(new DateTimeImmutable('2026-09-09 09:00:01', $zone)),
        );
    }
}
