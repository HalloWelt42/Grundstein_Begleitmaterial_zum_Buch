<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Geldbetrag;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Unit-Tests für das Wertobjekt Geldbetrag. Reiner Domänen-Code ist der am
 * leichtesten prüfbare überhaupt: keine Datenbank, keine Doubles, keine
 * Verdrahtung - nur Eingabe und erwartete Ausgabe (Kapitel 46, 54).
 */
final class GeldbetragTest extends TestCase
{
    #[Test]
    public function ein_negativer_betrag_ist_unmoeglich(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Geldbetrag(-1);
    }

    #[Test]
    public function plus_liefert_ein_neues_objekt_und_laesst_das_alte_unberuehrt(): void
    {
        $betrag   = new Geldbetrag(1000);
        $groesser = $betrag->plus(new Geldbetrag(500));

        // Das Ergebnis stimmt ...
        self::assertSame(1500, $groesser->cent);
        // ... und das Original ist unverändert geblieben (Unveränderlichkeit).
        self::assertSame(1000, $betrag->cent);
    }

    #[Test]
    public function gleiche_werte_bedeuten_gleichheit(): void
    {
        // Zwei getrennt erzeugte, aber wertgleiche Beträge sind gleich.
        self::assertTrue((new Geldbetrag(4990))->istGleich(new Geldbetrag(4990)));
        self::assertFalse((new Geldbetrag(4990))->istGleich(new Geldbetrag(5000)));
    }

    #[Test]
    public function aus_euro_rundet_auf_ganze_cent(): void
    {
        self::assertSame(4990, Geldbetrag::ausEuro(49.90)->cent);
    }

    #[Test]
    public function als_text_formatiert_deutsch(): void
    {
        self::assertSame('49,90 EUR', (new Geldbetrag(4990))->alsText());
    }
}
