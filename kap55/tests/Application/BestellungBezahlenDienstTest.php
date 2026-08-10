<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Adapter\Payment\BegrenzteZahlung;
use App\Adapter\Persistence\InMemoryBestellungen;
use App\Application\BestellungBezahlenDienst;
use App\Application\BestellungNichtGefunden;
use App\Application\ZahlungAbgelehnt;
use App\Domain\Bestellung;
use App\Domain\Geld;
use App\Tests\Double\ZusagendeZahlung;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Der Unit-Test des Anwendungsdienstes - ohne Datenbank, ohne Netz. Hinter dem
 * Persistenz-Port steckt der In-Memory-Adapter, hinter dem Zahlungs-Port ein
 * Test-Double. Weil der Dienst nur seine Ports kennt, prüft dieser Test
 * dieselbe Fachlogik, die später in Produktion läuft.
 */
final class BestellungBezahlenDienstTest extends TestCase
{
    #[Test]
    public function bezahlt_eine_offene_bestellung_und_belastet_den_betrag(): void
    {
        $bestellungen = new InMemoryBestellungen();
        $zahlung      = new ZusagendeZahlung();
        $dienst       = new BestellungBezahlenDienst($bestellungen, $zahlung);

        $offen = $bestellungen->save(Bestellung::neu('Ada', Geld::ausEuro(49.90)));

        $bezahlt = $dienst->fuehreAus($offen->id ?? 0);

        // Ergebnis: die Bestellung ist jetzt bezahlt ...
        self::assertTrue($bezahlt->bezahlt);
        // ... und der Betrag wurde genau einmal belastet (Interaktion prüfen).
        self::assertCount(1, $zahlung->belastungen);
        self::assertSame(4990, $zahlung->belastungen[0]->cent);
    }

    #[Test]
    public function gibt_die_bestellung_mit_gleicher_id_und_kunde_zurueck(): void
    {
        $bestellungen = new InMemoryBestellungen();
        $dienst       = new BestellungBezahlenDienst($bestellungen, new ZusagendeZahlung());

        $offen   = $bestellungen->save(Bestellung::neu('Ada', Geld::ausEuro(49.90)));
        $bezahlt = $dienst->fuehreAus($offen->id ?? 0);

        // Dieselbe Identität, nur der Zustand hat sich geändert.
        self::assertSame($offen->id, $bezahlt->id);
        self::assertSame('Ada', $bezahlt->kunde);
    }

    #[Test]
    public function belastet_eine_bereits_bezahlte_bestellung_nicht_erneut(): void
    {
        $bestellungen = new InMemoryBestellungen();
        $zahlung      = new ZusagendeZahlung();
        $dienst       = new BestellungBezahlenDienst($bestellungen, $zahlung);

        $offen = $bestellungen->save(Bestellung::neu('Ada', Geld::ausEuro(49.90)));

        // Zweimal denselben Anwendungsfall aufrufen - der zweite Aufruf darf
        // nicht ein zweites Mal belasten.
        $erste  = $dienst->fuehreAus($offen->id ?? 0);
        $zweite = $dienst->fuehreAus($offen->id ?? 0);

        self::assertTrue($erste->bezahlt);
        self::assertTrue($zweite->bezahlt);
        self::assertCount(1, $zahlung->belastungen);
    }

    #[Test]
    public function wirft_wenn_die_bestellung_fehlt(): void
    {
        $dienst = new BestellungBezahlenDienst(
            new InMemoryBestellungen(),
            new ZusagendeZahlung(),
        );

        $this->expectException(BestellungNichtGefunden::class);

        $dienst->fuehreAus(999);
    }

    #[Test]
    public function reicht_eine_abgelehnte_zahlung_nach_oben(): void
    {
        $bestellungen = new InMemoryBestellungen();
        // Limit 10 EUR - die 49,90-EUR-Bestellung wird abgelehnt.
        $dienst = new BestellungBezahlenDienst(
            $bestellungen,
            new BegrenzteZahlung(Geld::ausEuro(10.0)),
        );

        $offen = $bestellungen->save(Bestellung::neu('Ada', Geld::ausEuro(49.90)));

        $this->expectException(ZahlungAbgelehnt::class);

        $dienst->fuehreAus($offen->id ?? 0);
    }
}
