<?php

declare(strict_types=1);

namespace App\Tests;

use App\Gutschein;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Die Gültigkeitsprüfung ist eine reine Funktion des Prüfzeitpunkts und
 * braucht daher kein Double - nur ein Wertobjekt mit festen Zeiten und
 * ein paar Grenzfälle. Besonders lohnend ist die Sekunde genau auf der
 * Ablaufgrenze und die Sekunde danach.
 */
#[CoversClass(Gutschein::class)]
final class GutscheinTest extends TestCase
{
    private function gutscheinGueltigBis(string $bis): Gutschein
    {
        $zone = new DateTimeZone('UTC');

        return new Gutschein(
            1000,
            new DateTimeImmutable('2026-08-10 09:00:00', $zone),
            new DateTimeImmutable($bis, $zone),
        );
    }

    #[Test]
    public function ist_exakt_auf_der_ablaufgrenze_noch_gueltig(): void
    {
        $gutschein = $this->gutscheinGueltigBis('2026-09-09 09:00:00');

        self::assertTrue(
            $gutschein->istGueltigAm(
                new DateTimeImmutable('2026-09-09 09:00:00', new DateTimeZone('UTC')),
            ),
        );
    }

    #[Test]
    public function ist_eine_sekunde_nach_ablauf_ungueltig(): void
    {
        $gutschein = $this->gutscheinGueltigBis('2026-09-09 09:00:00');

        self::assertFalse(
            $gutschein->istGueltigAm(
                new DateTimeImmutable('2026-09-09 09:00:01', new DateTimeZone('UTC')),
            ),
        );
    }
}
