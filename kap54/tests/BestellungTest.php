<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Bestellposten;
use App\Domain\Bestellstatus;
use App\Domain\Bestellung;
use App\Domain\Geldbetrag;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BestellungTest extends TestCase
{
    #[Test]
    public function die_gesamtsumme_addiert_alle_posten(): void
    {
        $bestellung = new Bestellung(1001, kundenId: 7);
        $bestellung->fuegeHinzu(new Bestellposten('Tastatur', Geldbetrag::inEuro(80), 1));
        $bestellung->fuegeHinzu(new Bestellposten('Kabel', Geldbetrag::inEuro(5), 4));

        // 80 EUR + 4 mal 5 EUR = 100 EUR.
        self::assertTrue($bestellung->gesamtsumme()->istGleich(Geldbetrag::inEuro(100)));
    }

    #[Test]
    public function eine_leere_bestellung_kann_nicht_bezahlt_werden(): void
    {
        $this->expectException(DomainException::class);

        (new Bestellung(1001, kundenId: 7))->bezahle();
    }

    #[Test]
    public function nach_dem_bezahlen_kommt_kein_posten_mehr_hinzu(): void
    {
        $bestellung = new Bestellung(1001, kundenId: 7);
        $bestellung->fuegeHinzu(new Bestellposten('Tastatur', Geldbetrag::inEuro(80), 1));
        $bestellung->bezahle();

        self::assertSame(Bestellstatus::Bezahlt, $bestellung->status());

        $this->expectException(DomainException::class);
        $bestellung->fuegeHinzu(new Bestellposten('Maus', Geldbetrag::inEuro(25), 1));
    }

    #[Test]
    public function eine_bezahlte_bestellung_kann_nicht_storniert_werden(): void
    {
        $bestellung = new Bestellung(1001, kundenId: 7);
        $bestellung->fuegeHinzu(new Bestellposten('Tastatur', Geldbetrag::inEuro(80), 1));
        $bestellung->bezahle();

        $this->expectException(DomainException::class);
        $bestellung->storniere();
    }

    #[Test]
    public function ein_posten_in_fremder_waehrung_wird_abgewiesen(): void
    {
        $bestellung = new Bestellung(1001, kundenId: 7, waehrung: 'EUR');

        $this->expectException(DomainException::class);
        $bestellung->fuegeHinzu(new Bestellposten('Import', new Geldbetrag(1000, 'USD'), 1));
    }

    #[Test]
    public function gleichheit_laeuft_ueber_die_identitaet(): void
    {
        $eine = new Bestellung(1001, kundenId: 7);
        $eine->fuegeHinzu(new Bestellposten('Tastatur', Geldbetrag::inEuro(80), 1));

        // Gleiche id, aber ganz anderer Inhalt - trotzdem dieselbe Bestellung.
        $andere = new Bestellung(1001, kundenId: 99);

        self::assertTrue($eine->istGleich($andere));
    }
}
