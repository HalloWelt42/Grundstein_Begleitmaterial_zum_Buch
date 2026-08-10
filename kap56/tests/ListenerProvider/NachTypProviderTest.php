<?php

declare(strict_types=1);

namespace App\Tests\ListenerProvider;

use App\Domain\Geld;
use App\Event\BestellungBezahlt;
use App\Event\KundeBenachrichtigen;
use App\ListenerProvider\NachTypProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Die Prüfungen des Providers: Liefert er die angemeldeten Zuhörer in der
 * richtigen Reihenfolge? Unterscheidet er die Ereignistypen sauber? Und
 * antwortet er mit einer leeren Liste, wenn niemand lauscht?
 */
final class NachTypProviderTest extends TestCase
{
    #[Test]
    public function liefert_die_angemeldeten_zuhoerer_in_reihenfolge(): void
    {
        $ersterZuhoerer  = static function (): void {};
        $zweiterZuhoerer = static function (): void {};

        $provider = new NachTypProvider();
        $provider->lauscheAuf(BestellungBezahlt::class, $ersterZuhoerer);
        $provider->lauscheAuf(BestellungBezahlt::class, $zweiterZuhoerer);

        $gefunden = [...$provider->getListenersForEvent(
            new BestellungBezahlt(1, 'Ada', Geld::ausEuro(10.0)),
        )];

        self::assertSame([$ersterZuhoerer, $zweiterZuhoerer], $gefunden);
    }

    #[Test]
    public function unterscheidet_die_ereignistypen(): void
    {
        $fuerBestellung = static function (): void {};

        $provider = new NachTypProvider();
        $provider->lauscheAuf(BestellungBezahlt::class, $fuerBestellung);

        // Für ein anderes Ereignis gibt es keinen Zuhörer.
        $andere = [...$provider->getListenersForEvent(
            new KundeBenachrichtigen('Ada', 'Hallo'),
        )];

        self::assertSame([], $andere);
    }

    #[Test]
    public function liefert_nichts_fuer_einen_unbekannten_typ(): void
    {
        $provider = new NachTypProvider();

        $gefunden = [...$provider->getListenersForEvent(
            new BestellungBezahlt(1, 'Ada', Geld::ausEuro(10.0)),
        )];

        self::assertSame([], $gefunden);
    }
}
