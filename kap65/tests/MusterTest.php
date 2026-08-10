<?php

declare(strict_types=1);

namespace App\Tests;

use App\Muster;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Muster::class)]
final class MusterTest extends TestCase
{
    /**
     * Ein Datenanbieter prüft mehrere Muster mit vielen Fällen auf einmal.
     *
     * @return iterable<string, array{string, string, bool}>
     */
    public static function faelle(): iterable
    {
        yield 'PLZ gültig'          => [Muster::PLZ, '21335', true];
        yield 'PLZ zu kurz'         => [Muster::PLZ, '2133', false];
        yield 'PLZ mit Buchstabe'   => [Muster::PLZ, '2133x', false];

        yield 'Name mit Umlaut'      => [Muster::NAME, 'Grüße', true];
        yield 'Name mit Bindestrich' => [Muster::NAME, 'Anna-Lena', true];
        yield 'Name mit Ziffer'      => [Muster::NAME, 'Anna2', false];

        yield 'Hex kurz'            => [Muster::HEX_FARBE, '#a0f', true];
        yield 'Hex lang groß'       => [Muster::HEX_FARBE, '#AA00FF', true];
        yield 'Hex ohne Raute'      => [Muster::HEX_FARBE, 'aa00ff', false];
        yield 'Hex falsche Ziffer'  => [Muster::HEX_FARBE, '#gg0011', false];
    }

    #[Test]
    #[DataProvider('faelle')]
    public function passt_urteilt_korrekt(string $muster, string $wert, bool $erwartet): void
    {
        self::assertSame($erwartet, Muster::passt($muster, $wert));
    }

    #[Test]
    public function datumsteile_zerlegt_ein_gueltiges_datum(): void
    {
        self::assertSame(
            ['jahr' => 2026, 'monat' => 8, 'tag' => 10],
            Muster::datumsteile('2026-08-10'),
        );
    }

    #[Test]
    public function datumsteile_gibt_bei_unsinn_null_zurueck(): void
    {
        self::assertNull(Muster::datumsteile('10.08.2026'));
    }
}
