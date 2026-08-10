<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Bestellstatus;
use App\Domain\Bestellung;
use App\Domain\EmailAdresse;
use App\Domain\Geldbetrag;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Unit-Tests für die Entity Bestellung. Sie prüfen die Invariante im
 * Konstruktor und den bewachten Zustandswechsel - ganz ohne Infrastruktur
 * (Kapitel 54).
 */
final class BestellungTest extends TestCase
{
    private function bestellung(): Bestellung
    {
        return Bestellung::neu(
            new EmailAdresse('ada@example.org'),
            Geldbetrag::ausEuro(49.90),
        );
    }

    #[Test]
    public function eine_neue_bestellung_ist_offen_und_hat_keine_id(): void
    {
        $bestellung = $this->bestellung();

        self::assertNull($bestellung->id);
        self::assertSame(Bestellstatus::Neu, $bestellung->status);
        self::assertFalse($bestellung->istBezahlt());
    }

    #[Test]
    public function eine_bestellung_ueber_null_euro_ist_unmoeglich(): void
    {
        // Der Geldbetrag lässt null zu, die Bestellung nicht - ihre Invariante.
        $this->expectException(DomainException::class);

        Bestellung::neu(new EmailAdresse('ada@example.org'), Geldbetrag::ausEuro(0.0));
    }

    #[Test]
    public function bezahlen_wechselt_den_status_und_erzeugt_ein_neues_objekt(): void
    {
        $offen   = $this->bestellung();
        $bezahlt = $offen->bezahle();

        // Neues Objekt mit neuem Status, das alte bleibt offen.
        self::assertTrue($bezahlt->istBezahlt());
        self::assertFalse($offen->istBezahlt());
    }

    #[Test]
    public function eine_bezahlte_bestellung_wird_nicht_erneut_bezahlt(): void
    {
        $bezahlt = $this->bestellung()->bezahle();

        $this->expectException(DomainException::class);
        $bezahlt->bezahle();
    }
}
