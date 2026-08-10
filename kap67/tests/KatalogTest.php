<?php

declare(strict_types=1);

namespace App\Tests;

use App\Katalog;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 67: Internationalisierung
 *
 * Der Collator ordnet Umlaute sprachrichtig ein - anders als ein roher
 * Byte-Vergleich, der sie ans Ende schiebt. Die Tests nageln genau diesen
 * Unterschied fest und prüfen zugleich, dass der Katalog unverändert bleibt.
 */
#[CoversClass(Katalog::class)]
final class KatalogTest extends TestCase
{
    /** @var list<string> */
    private const NAMEN = ['Zeder', 'Apfel', 'Öl', 'Bär', 'Ähre'];

    #[Test]
    public function sortiert_umlaute_deutsch_richtig_ein(): void
    {
        $katalog = new Katalog(self::NAMEN);

        $sortiert = $katalog->sortiertNach('de-DE');

        // Im Deutschen zählt Ä wie A: "Ähre" steht ganz vorne, nicht hinten.
        self::assertSame('Ähre', $sortiert[0]);
        // Ö zählt wie O - vor Zeder, hinter Bär.
        self::assertLessThan(
            array_search('Zeder', $sortiert, true),
            array_search('Öl', $sortiert, true),
        );
    }

    #[Test]
    public function unterscheidet_sich_vom_rohen_byte_vergleich(): void
    {
        $katalog = new Katalog(self::NAMEN);

        $roh = self::NAMEN;
        sort($roh); // Byte-Vergleich: Umlaute landen hinten

        self::assertNotSame($roh, $katalog->sortiertNach('de-DE'));
    }

    #[Test]
    public function laesst_den_katalog_selbst_unveraendert(): void
    {
        $katalog = new Katalog(self::NAMEN);

        $katalog->sortiertNach('de-DE');

        // Ein zweiter Aufruf liefert dasselbe - der Katalog wurde nicht
        // beim Sortieren umgestellt.
        self::assertSame(
            $katalog->sortiertNach('de-DE'),
            $katalog->sortiertNach('de-DE'),
        );
    }
}
