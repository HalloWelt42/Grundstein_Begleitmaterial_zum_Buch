<?php

declare(strict_types=1);

namespace App\Tests;

use App\Gutschein;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben
 *
 * Prüft die reine Funktion istGueltigAm(). Weil sie keine versteckte
 * Zeit und keine Abhängigkeiten hat, braucht dieser Test kein einziges
 * Double: Wir bauen einen Gutschein mit festen Werten und geben den
 * Prüfzeitpunkt als Argument herein - inklusive der Grenzfälle.
 */
#[CoversClass(Gutschein::class)]
final class GutscheinTest extends TestCase
{
    #[Test]
    public function ist_am_tag_der_ausstellung_gueltig(): void
    {
        $gutschein = $this->gutscheinGueltigBis('2026-09-09 12:00:00');

        self::assertTrue(
            $gutschein->istGueltigAm(new DateTimeImmutable('2026-08-10 12:00:00')),
        );
    }

    #[Test]
    public function ist_exakt_auf_der_ablaufgrenze_noch_gueltig(): void
    {
        $gutschein = $this->gutscheinGueltigBis('2026-09-09 12:00:00');

        self::assertTrue(
            $gutschein->istGueltigAm(new DateTimeImmutable('2026-09-09 12:00:00')),
        );
    }

    #[Test]
    public function ist_eine_sekunde_nach_ablauf_ungueltig(): void
    {
        $gutschein = $this->gutscheinGueltigBis('2026-09-09 12:00:00');

        self::assertFalse(
            $gutschein->istGueltigAm(new DateTimeImmutable('2026-09-09 12:00:01')),
        );
    }

    private function gutscheinGueltigBis(string $gueltigBis): Gutschein
    {
        return new Gutschein(
            'ABCD1234',
            1000,
            new DateTimeImmutable('2026-08-10 12:00:00'),
            new DateTimeImmutable($gueltigBis),
        );
    }
}
