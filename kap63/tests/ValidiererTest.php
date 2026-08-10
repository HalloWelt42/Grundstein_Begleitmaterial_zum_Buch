<?php

declare(strict_types=1);

namespace App\Tests;

use App\Attribut\Bereich;
use App\Attribut\MaxLaenge;
use App\Attribut\NichtLeer;
use App\Registrierung;
use App\Validierer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Validierer::class)]
#[CoversClass(NichtLeer::class)]
#[CoversClass(MaxLaenge::class)]
#[CoversClass(Bereich::class)]
final class ValidiererTest extends TestCase
{
    #[Test]
    public function gueltige_daten_ergeben_keine_verstoesse(): void
    {
        $verstoesse = (new Validierer())->pruefe(
            new Registrierung(name: 'Ada', email: 'ada@example.org', alter: 36),
        );

        self::assertSame([], $verstoesse);
    }

    #[Test]
    public function jede_verletzte_regel_liefert_genau_einen_verstoss(): void
    {
        $verstoesse = (new Validierer())->pruefe(
            new Registrierung(
                name: '   ',
                email: str_repeat('x', 60) . '@example.org',
                alter: 7,
            ),
        );

        // Aus drei Feldern je ein Verstoss - in der Reihenfolge der Felder.
        $als_text = array_map(static fn ($v): string => $v->alsText(), $verstoesse);

        self::assertSame([
            'name: darf nicht leer sein',
            'email: darf höchstens 50 Zeichen haben',
            'alter: muss zwischen 18 und 120 liegen',
        ], $als_text);
    }

    #[Test]
    public function das_alter_ist_an_der_unteren_grenze_gueltig(): void
    {
        $verstoesse = (new Validierer())->pruefe(
            new Registrierung(name: 'Grace', email: 'grace@example.org', alter: 18),
        );

        self::assertSame([], $verstoesse);
    }

    #[Test]
    public function eine_einzelne_regel_prueft_ihren_wert_direkt(): void
    {
        // Die Attribute sind gewöhnliche Objekte und lassen sich auch ohne
        // Reflection als reine Regeln prüfen.
        self::assertNull((new NichtLeer())->pruefe('Ada'));
        self::assertSame('darf nicht leer sein', (new NichtLeer())->pruefe('  '));

        self::assertNull((new MaxLaenge(3))->pruefe('abc'));
        self::assertSame('darf höchstens 3 Zeichen haben', (new MaxLaenge(3))->pruefe('abcd'));

        self::assertNull((new Bereich(1, 10))->pruefe(10));
        self::assertSame('muss zwischen 1 und 10 liegen', (new Bereich(1, 10))->pruefe(11));
    }
}
