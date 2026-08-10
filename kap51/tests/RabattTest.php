<?php

declare(strict_types=1);

namespace App\Tests;

use App\Rabatt;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 51: Continuous Integration
 *
 * Die Tests zur Rabatt-Klasse. Genau diese Fälle führt die Prüfkette
 * bei jedem Push automatisch aus - grün heißt, alle sind erfüllt.
 */
#[CoversClass(Rabatt::class)]
final class RabattTest extends TestCase
{
    /**
     * Ein Datenanbieter liefert mehrere Fälle an denselben Test.
     *
     * @return iterable<string, array{int, int, int}>
     */
    public static function faelle(): iterable
    {
        // Beschreibung => [Preis in Cent, Prozent, erwarteter Preis in Cent]
        yield 'kein Rabatt lässt den Preis unberührt' => [1000, 0, 1000];
        yield 'zehn Prozent auf zehn Euro' => [1000, 10, 900];
        yield 'voller Rabatt ergibt null' => [1000, 100, 0];
        yield 'kaufmännische Rundung auf den Cent' => [999, 50, 499];
    }

    #[Test]
    #[DataProvider('faelle')]
    public function berechnet_den_rabattierten_preis(int $preis, int $prozent, int $erwartet): void
    {
        self::assertSame($erwartet, (new Rabatt())->anwenden($preis, $prozent));
    }

    #[Test]
    public function weist_einen_negativen_preis_ab(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Rabatt())->anwenden(-1, 10);
    }

    #[Test]
    public function weist_einen_rabatt_von_mehr_als_hundert_prozent_ab(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Rabatt())->anwenden(1000, 120);
    }
}
