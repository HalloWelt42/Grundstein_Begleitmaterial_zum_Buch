<?php

declare(strict_types=1);

namespace App\Tests\EventDispatcher;

use App\Domain\Geld;
use App\Event\BestellungBezahlt;
use App\Event\KundeBenachrichtigen;
use App\EventDispatcher\EventDispatcher;
use App\ListenerProvider\NachTypProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Die Prüfungen des Dispatchers: Ruft er die richtigen Zuhörer? In der
 * richtigen Reihenfolge? Gibt er das Ereignis zurück? Und hält er bei einem
 * stoppbaren Ereignis rechtzeitig an?
 */
final class EventDispatcherTest extends TestCase
{
    #[Test]
    public function ruft_die_zuhoerer_des_ereignistyps(): void
    {
        $gerufen = false;

        $provider = new NachTypProvider();
        $provider->lauscheAuf(
            BestellungBezahlt::class,
            function (BestellungBezahlt $ereignis) use (&$gerufen): void {
                $gerufen = true;
            },
        );

        (new EventDispatcher($provider))->dispatch(
            new BestellungBezahlt(1, 'Ada', Geld::ausEuro(10.0)),
        );

        self::assertTrue($gerufen);
    }

    #[Test]
    public function ruft_keinen_zuhoerer_eines_anderen_typs(): void
    {
        $gerufen = false;

        // Zuhörer nur auf KundeBenachrichtigen - aber verteilt wird
        // BestellungBezahlt. Der Zuhörer darf nicht anspringen.
        $provider = new NachTypProvider();
        $provider->lauscheAuf(
            KundeBenachrichtigen::class,
            function () use (&$gerufen): void {
                $gerufen = true;
            },
        );

        (new EventDispatcher($provider))->dispatch(
            new BestellungBezahlt(1, 'Ada', Geld::ausEuro(10.0)),
        );

        self::assertFalse($gerufen);
    }

    #[Test]
    public function ruft_die_zuhoerer_in_der_reihenfolge_der_anmeldung(): void
    {
        $spur = [];

        $provider = new NachTypProvider();
        $provider->lauscheAuf(BestellungBezahlt::class, function () use (&$spur): void {
            $spur[] = 'a';
        });
        $provider->lauscheAuf(BestellungBezahlt::class, function () use (&$spur): void {
            $spur[] = 'b';
        });
        $provider->lauscheAuf(BestellungBezahlt::class, function () use (&$spur): void {
            $spur[] = 'c';
        });

        (new EventDispatcher($provider))->dispatch(
            new BestellungBezahlt(1, 'Ada', Geld::ausEuro(10.0)),
        );

        self::assertSame(['a', 'b', 'c'], $spur);
    }

    #[Test]
    public function gibt_dasselbe_ereignis_zurueck(): void
    {
        $ereignis = new BestellungBezahlt(1, 'Ada', Geld::ausEuro(10.0));

        $zurueck = (new EventDispatcher(new NachTypProvider()))->dispatch($ereignis);

        // PSR-14: dispatch() liefert genau das übergebene Ereignis.
        self::assertSame($ereignis, $zurueck);
    }

    #[Test]
    public function haelt_die_verbreitung_an_sobald_ein_zuhoerer_stoppt(): void
    {
        $spur = [];

        $provider = new NachTypProvider();
        // Der erste Zuhörer beendet die Verbreitung ...
        $provider->lauscheAuf(
            KundeBenachrichtigen::class,
            function (KundeBenachrichtigen $ereignis) use (&$spur): void {
                $spur[] = 'erster';
                $ereignis->stoppeVerbreitung();
            },
        );
        // ... der zweite darf danach nicht mehr laufen.
        $provider->lauscheAuf(KundeBenachrichtigen::class, function () use (&$spur): void {
            $spur[] = 'zweiter';
        });

        (new EventDispatcher($provider))->dispatch(
            new KundeBenachrichtigen('Ada', 'Hallo'),
        );

        self::assertSame(['erster'], $spur);
    }

    #[Test]
    public function verteilt_ein_bereits_gestopptes_ereignis_gar_nicht(): void
    {
        $gerufen = false;

        $provider = new NachTypProvider();
        $provider->lauscheAuf(KundeBenachrichtigen::class, function () use (&$gerufen): void {
            $gerufen = true;
        });

        $ereignis = new KundeBenachrichtigen('Ada', 'Hallo');
        $ereignis->stoppeVerbreitung(); // schon vor dem Verteilen gestoppt

        (new EventDispatcher($provider))->dispatch($ereignis);

        self::assertFalse($gerufen);
    }
}
