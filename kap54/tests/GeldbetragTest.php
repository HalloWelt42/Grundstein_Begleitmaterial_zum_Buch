<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Geldbetrag;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Prüft die drei Zusagen eines Wertobjekts: Gleichheit über den Wert,
 * Unveränderlichkeit und Gültigkeit ab der Erzeugung.
 */
final class GeldbetragTest extends TestCase
{
    #[Test]
    public function gleicher_wert_bedeutet_gleichheit(): void
    {
        $a = new Geldbetrag(1299, 'EUR');
        $b = Geldbetrag::inEuro(12, 99);

        self::assertTrue($a->istGleich($b));
    }

    #[Test]
    public function verschiedene_waehrung_ist_nicht_gleich(): void
    {
        $a = new Geldbetrag(1000, 'EUR');
        $b = new Geldbetrag(1000, 'USD');

        self::assertFalse($a->istGleich($b));
    }

    #[Test]
    public function plus_liefert_ein_neues_objekt_und_laesst_das_alte_unberuehrt(): void
    {
        $betrag = new Geldbetrag(1000);
        $groesser = $betrag->plus(new Geldbetrag(500));

        // Das Ergebnis stimmt ...
        self::assertSame(1500, $groesser->cent);
        // ... und das Original ist unverändert geblieben.
        self::assertSame(1000, $betrag->cent);
    }

    #[Test]
    public function ein_negativer_betrag_ist_unmoeglich(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Geldbetrag(-1);
    }

    #[Test]
    public function ein_ungueltiger_waehrungscode_ist_unmoeglich(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Geldbetrag(1000, 'Euro');
    }

    #[Test]
    public function addition_verschiedener_waehrungen_wird_abgewiesen(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Geldbetrag(1000, 'EUR'))->plus(new Geldbetrag(1000, 'USD'));
    }

    #[Test]
    public function mal_vervielfacht_den_betrag(): void
    {
        $betrag = new Geldbetrag(250);

        self::assertSame(1000, $betrag->mal(4)->cent);
    }

    #[Test]
    public function anteil_rechnet_ganzzahlig_ohne_cent_bruchteile(): void
    {
        // 10 Prozent von 999 Cent sind 99,9 - ganzzahlig also 99 Cent.
        self::assertSame(99, (new Geldbetrag(999))->anteil(10)->cent);
    }

    #[Test]
    public function als_text_formatiert_deutsch(): void
    {
        self::assertSame('12,99 EUR', (new Geldbetrag(1299))->alsText());
    }
}
