<?php

declare(strict_types=1);

namespace Tests;

use App\Preis;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * Ein kleiner Vorgeschmack auf das nächste Kapitel: dieselben
 * Behauptungen wie im handgeschriebenen Test, aber mit PHPUnit. Statt
 * eigener pruefe()-Funktion gibt es fertige assert-Methoden, und der
 * Test-Runner zählt, meldet und färbt grün oder rot von selbst.
 */
#[CoversClass(Preis::class)]
final class PreisTest extends TestCase
{
    #[Test]
    public function rabatt_zieht_den_richtigen_betrag_ab(): void
    {
        $preis = new Preis(1000);

        self::assertSame(800, $preis->mitRabatt(20)->cent);
    }

    #[Test]
    public function der_urspruengliche_preis_bleibt_unveraendert(): void
    {
        $preis = new Preis(1000);
        $preis->mitRabatt(50);

        self::assertSame(1000, $preis->cent);
    }

    #[Test]
    public function ein_negativer_preis_wird_abgelehnt(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Preis(-1);
    }
}
