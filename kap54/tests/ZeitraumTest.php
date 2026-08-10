<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Zeitraum;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ZeitraumTest extends TestCase
{
    #[Test]
    public function ein_ende_vor_dem_anfang_ist_unmoeglich(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Zeitraum(
            new DateTimeImmutable('2026-08-31'),
            new DateTimeImmutable('2026-08-01'),
        );
    }

    #[Test]
    public function ein_zeitpunkt_innerhalb_wird_erkannt(): void
    {
        $zeitraum = new Zeitraum(
            new DateTimeImmutable('2026-08-01'),
            new DateTimeImmutable('2026-08-31'),
        );

        self::assertTrue($zeitraum->enthaelt(new DateTimeImmutable('2026-08-10')));
        self::assertFalse($zeitraum->enthaelt(new DateTimeImmutable('2026-09-01')));
    }

    #[Test]
    public function ueberschneidung_wird_erkannt(): void
    {
        $august = new Zeitraum(
            new DateTimeImmutable('2026-08-01'),
            new DateTimeImmutable('2026-08-31'),
        );
        $mitteAugustSeptember = new Zeitraum(
            new DateTimeImmutable('2026-08-15'),
            new DateTimeImmutable('2026-09-15'),
        );
        $oktober = new Zeitraum(
            new DateTimeImmutable('2026-10-01'),
            new DateTimeImmutable('2026-10-31'),
        );

        self::assertTrue($august->ueberschneidetSich($mitteAugustSeptember));
        self::assertFalse($august->ueberschneidetSich($oktober));
    }

    #[Test]
    public function gleiche_grenzen_bedeuten_gleichheit(): void
    {
        $a = new Zeitraum(
            new DateTimeImmutable('2026-08-01 00:00:00'),
            new DateTimeImmutable('2026-08-31 00:00:00'),
        );
        $b = new Zeitraum(
            new DateTimeImmutable('2026-08-01 00:00:00'),
            new DateTimeImmutable('2026-08-31 00:00:00'),
        );

        // Zwei verschiedene Objekte, aber derselbe Wert.
        self::assertTrue($a->istGleich($b));
    }
}
