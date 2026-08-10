<?php

declare(strict_types=1);

namespace App\Tests;

use App\Faul;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Faul::class)]
final class FaulTest extends TestCase
{
    #[Test]
    public function map_bildet_jeden_wert_ab(): void
    {
        $ergebnis = iterator_to_array(
            Faul::map([1, 2, 3], static fn (int $n): int => $n * 10),
        );

        self::assertSame([10, 20, 30], $ergebnis);
    }

    #[Test]
    public function filter_behaelt_nur_passende_werte(): void
    {
        $ergebnis = iterator_to_array(
            Faul::filter([1, 2, 3, 4], static fn (int $n): bool => $n % 2 === 0),
            false, // Schlüssel neu nummerieren, um nur die Werte zu prüfen
        );

        self::assertSame([2, 4], $ergebnis);
    }

    #[Test]
    public function nimm_begrenzt_auf_die_ersten_n_werte(): void
    {
        $ergebnis = iterator_to_array(
            Faul::nimm([10, 20, 30, 40], 2),
            false,
        );

        self::assertSame([10, 20], $ergebnis);
    }

    #[Test]
    public function nimm_zieht_aus_einer_unendlichen_quelle_nur_so_viel_wie_noetig(): void
    {
        $gezogen = 0;

        // Eine unendliche Quelle: Ohne die Faulheit von nimm() liefe sie ewig.
        $unendlich = (static function () use (&$gezogen): Generator {
            $n = 1;
            while (true) {
                $gezogen++;
                yield $n++;
            }
        })();

        $ergebnis = iterator_to_array(Faul::nimm($unendlich, 3), false);

        self::assertSame([1, 2, 3], $ergebnis);
        // Der Beweis der Faulheit: genau drei Werte gezogen, nicht mehr.
        self::assertSame(3, $gezogen);
    }

    #[Test]
    public function die_bausteine_lassen_sich_faul_verketten(): void
    {
        $strom = Faul::nimm(
            Faul::filter(
                Faul::map(range(1, 100), static fn (int $n): int => $n * $n),
                static fn (int $q): bool => $q % 2 === 0,
            ),
            5,
        );

        // Gerade Quadratzahlen: 4, 16, 36, 64, 100.
        self::assertSame([4, 16, 36, 64, 100], iterator_to_array($strom, false));
    }
}
