<?php

declare(strict_types=1);

namespace App\Tests;

use App\Position;
use App\Warenkorb;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Warenkorb::class)]
#[CoversClass(Position::class)]
final class WarenkorbTest extends TestCase
{
    private Warenkorb $korb;

    /**
     * Vor jedem einzelnen Test bekommen wir einen frischen, leeren Korb.
     * So kann kein Test das Ergebnis eines anderen beeinflussen.
     */
    protected function setUp(): void
    {
        $this->korb = new Warenkorb();
    }

    #[Test]
    public function ein_neuer_korb_ist_leer(): void
    {
        $this->assertTrue($this->korb->istLeer());
        $this->assertSame(0, $this->korb->anzahl());
    }

    #[Test]
    public function nach_dem_legen_ist_der_korb_nicht_mehr_leer(): void
    {
        $this->korb->lege(new Position('Tastatur', 5000, 1));

        $this->assertFalse($this->korb->istLeer());
        $this->assertCount(1, $this->korb->positionen());
    }

    #[Test]
    public function die_zwischensumme_addiert_alle_positionen(): void
    {
        $this->korb->lege(new Position('Tastatur', 5000, 2));
        $this->korb->lege(new Position('Maus', 2500, 1));

        // assertSame prüft Wert UND Typ mit === - der Standardfall.
        $this->assertSame(12500, $this->korb->zwischensummeCent());
    }

    #[Test]
    public function eine_position_liefert_ein_position_objekt(): void
    {
        $this->korb->lege(new Position('Kabel', 999, 3));

        $erste = $this->korb->positionen()[0];

        $this->assertInstanceOf(Position::class, $erste);
        $this->assertSame(2997, $erste->gesamtpreisCent());
    }

    #[Test]
    public function ohne_rabatt_ist_der_endbetrag_die_zwischensumme(): void
    {
        $this->korb->lege(new Position('Buch', 1990, 1));

        $this->assertSame(1990, $this->korb->endbetragCent());
    }

    /**
     * Ein Datenanbieter speist denselben Test mit vielen Fällen.
     * Jeder Eintrag ist [rabattProzent, erwarteterEndbetrag] bei einer
     * Zwischensumme von 10000 Cent.
     *
     * @return array<string, array{int, int}>
     */
    public static function rabattFaelle(): array
    {
        return [
            'kein Rabatt'      => [0, 10000],
            'zehn Prozent'     => [10, 9000],
            'ein Viertel'      => [25, 7500],
            'die Hälfte'       => [50, 5000],
            'voller Nachlass'  => [100, 0],
        ];
    }

    #[Test]
    #[DataProvider('rabattFaelle')]
    public function der_rabatt_wird_korrekt_abgezogen(int $prozent, int $erwartet): void
    {
        $this->korb->lege(new Position('Ware', 10000, 1));

        $this->assertSame($erwartet, $this->korb->endbetragCent($prozent));
    }

    #[Test]
    public function ein_rabatt_ueber_hundert_wird_abgelehnt(): void
    {
        $this->korb->lege(new Position('Ware', 10000, 1));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Der Rabatt muss zwischen 0 und 100 liegen');

        $this->korb->endbetragCent(120);
    }

    #[Test]
    public function eine_menge_unter_eins_ist_ungueltig(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Position('Ware', 1000, 0);
    }
}
