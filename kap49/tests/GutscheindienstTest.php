<?php

declare(strict_types=1);

namespace App\Tests;

use App\Gutscheindienst;
use App\Tests\Double\FesteClock;
use App\Tests\Double\FesteCodeQuelle;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben
 *
 * Prüft den Dienst mit festgenagelten Abhängigkeiten. Weil Uhr und
 * Code-Quelle über den Konstruktor hereinkommen, setzen wir an diese
 * Nähte je einen Stub: eine feste Uhr und eine feste Code-Quelle. Dadurch
 * sind Code, Ausstellungszeit und Ablaufzeit im Voraus bekannt und exakt
 * prüfbar - unmöglich bei der Vorher-Fassung.
 */
#[CoversClass(Gutscheindienst::class)]
final class GutscheindienstTest extends TestCase
{
    #[Test]
    public function stellt_einen_gutschein_mit_festem_code_und_ablauf_aus(): void
    {
        $dienst = new Gutscheindienst(
            new FesteClock(new DateTimeImmutable('2026-08-10 09:00:00')),
            new FesteCodeQuelle('ABCD1234'),
        );

        $gutschein = $dienst->stelleAus(2500);

        self::assertSame('ABCD1234', $gutschein->code);
        self::assertSame(2500, $gutschein->wertCent);
        self::assertSame(
            '2026-08-10 09:00:00',
            $gutschein->erstelltAm->format('Y-m-d H:i:s'),
        );
        // Genau 30 Tage später - nur prüfbar, weil die Uhr fest steht.
        self::assertSame(
            '2026-09-09 09:00:00',
            $gutschein->gueltigBis->format('Y-m-d H:i:s'),
        );
    }
}
