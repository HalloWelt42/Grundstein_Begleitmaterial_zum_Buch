<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Bestellposten;
use App\Domain\Bestellung;
use App\Domain\EmailAdresse;
use App\Domain\Geldbetrag;
use App\Domain\Kunde;
use App\Domain\Preisfindung;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PreisfindungTest extends TestCase
{
    private function bestellungUeber100Euro(int $kundenId): Bestellung
    {
        $bestellung = new Bestellung(1001, $kundenId);
        $bestellung->fuegeHinzu(new Bestellposten('Tastatur', Geldbetrag::inEuro(80), 1));
        $bestellung->fuegeHinzu(new Bestellposten('Kabel', Geldbetrag::inEuro(5), 4));

        return $bestellung;
    }

    #[Test]
    public function ein_normaler_kunde_zahlt_die_volle_summe(): void
    {
        $kunde = new Kunde(7, 'Ada', new EmailAdresse('ada@example.org'));
        $endpreis = (new Preisfindung())->endpreis($this->bestellungUeber100Euro(7), $kunde);

        self::assertTrue($endpreis->istGleich(Geldbetrag::inEuro(100)));
    }

    #[Test]
    public function ein_stammkunde_erhaelt_zehn_prozent_rabatt(): void
    {
        $kunde = new Kunde(7, 'Ada', new EmailAdresse('ada@example.org'), stammkunde: true);
        $endpreis = (new Preisfindung())->endpreis($this->bestellungUeber100Euro(7), $kunde);

        // 100 EUR minus 10 Prozent = 90 EUR.
        self::assertTrue($endpreis->istGleich(Geldbetrag::inEuro(90)));
    }

    #[Test]
    public function eine_fremde_bestellung_wird_abgewiesen(): void
    {
        $kunde = new Kunde(7, 'Ada', new EmailAdresse('ada@example.org'));

        // Die Bestellung gehört Kunde 99, nicht Kunde 7.
        $fremde = $this->bestellungUeber100Euro(99);

        $this->expectException(DomainException::class);
        (new Preisfindung())->endpreis($fremde, $kunde);
    }
}
