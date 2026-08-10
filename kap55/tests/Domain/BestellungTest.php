<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Bestellung;
use App\Domain\Geld;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Die Domäne prüft sich ohne jede Infrastruktur - nicht einmal ein Double ist
 * nötig, weil die Bestellung ein unveränderliches Objekt mit ein wenig
 * Fachlogik ist. Reine Assertions genügen.
 */
final class BestellungTest extends TestCase
{
    #[Test]
    public function eine_neue_bestellung_ist_offen_und_hat_keine_id(): void
    {
        $bestellung = Bestellung::neu('Ada', Geld::ausEuro(49.90));

        self::assertNull($bestellung->id);
        self::assertTrue($bestellung->istOffen());
    }

    #[Test]
    public function als_bezahlt_markiert_schliesst_die_bestellung(): void
    {
        $offen   = Bestellung::neu('Ada', Geld::ausEuro(49.90));
        $bezahlt = $offen->alsBezahltMarkiert();

        // Unveränderlich: das alte Objekt bleibt offen, das neue ist bezahlt.
        self::assertTrue($offen->istOffen());
        self::assertFalse($bezahlt->istOffen());
        self::assertTrue($bezahlt->bezahlt);
    }

    #[Test]
    public function verweigert_das_doppelte_bezahlen(): void
    {
        $bezahlt = Bestellung::neu('Ada', Geld::ausEuro(49.90))->alsBezahltMarkiert();

        $this->expectException(LogicException::class);

        $bezahlt->alsBezahltMarkiert();
    }
}
