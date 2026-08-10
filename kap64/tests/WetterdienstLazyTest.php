<?php

declare(strict_types=1);

namespace App\Tests;

use App\LazyFabrik;
use App\Wetterdienst;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WetterdienstLazyTest extends TestCase
{
    protected function setUp(): void
    {
        // Jeder Test startet mit einem frischen Zähler.
        Wetterdienst::$konstruktionen = 0;
    }

    #[Test]
    public function ghost_laeuft_erst_beim_ersten_zugriff(): void
    {
        $dienst = LazyFabrik::ghost(
            Wetterdienst::class,
            static function (Wetterdienst $d): void {
                $d->__construct('Lüneburg');
            },
        );

        // Nur gebaut, aber noch nicht initialisiert.
        self::assertSame(0, Wetterdienst::$konstruktionen);

        // Der erste Zugriff löst den teuren Konstruktor aus.
        self::assertSame('Lüneburg', $dienst->stadt());
        self::assertSame(1, Wetterdienst::$konstruktionen);
    }

    #[Test]
    public function ghost_initialisiert_hoechstens_einmal(): void
    {
        $dienst = LazyFabrik::ghost(
            Wetterdienst::class,
            static function (Wetterdienst $d): void {
                $d->__construct('Kiel');
            },
        );

        // Mehrere Zugriffe, aber nur eine Konstruktion.
        $dienst->stadt();
        $dienst->temperatur();
        $dienst->stadt();

        self::assertSame(1, Wetterdienst::$konstruktionen);
    }

    #[Test]
    public function proxy_verweist_beim_zugriff_auf_das_echte_objekt(): void
    {
        $dienst = LazyFabrik::proxy(
            Wetterdienst::class,
            static fn (Wetterdienst $platzhalter): Wetterdienst => new Wetterdienst('Trier'),
        );

        self::assertSame(0, Wetterdienst::$konstruktionen);
        self::assertSame('Trier', $dienst->stadt());
        self::assertSame(1, Wetterdienst::$konstruktionen);
    }

    #[Test]
    public function ein_platzhalter_ist_vom_typ_seiner_klasse(): void
    {
        $dienst = LazyFabrik::ghost(
            Wetterdienst::class,
            static function (Wetterdienst $d): void {
                $d->__construct('Bremen');
            },
        );

        // Von außen ununterscheidbar vom echten Objekt - noch uninitialisiert.
        self::assertInstanceOf(Wetterdienst::class, $dienst);
        self::assertSame(0, Wetterdienst::$konstruktionen);
    }
}
