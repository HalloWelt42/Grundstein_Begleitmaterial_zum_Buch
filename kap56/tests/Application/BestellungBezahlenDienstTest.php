<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\BestellungBezahlenDienst;
use App\Domain\Bestellung;
use App\Domain\Geld;
use App\Event\BestellungBezahlt;
use App\EventDispatcher\EventDispatcher;
use App\Listener\Bestaetigungsmailer;
use App\Listener\Treuepunkte;
use App\Listener\Umsatzstatistik;
use App\ListenerProvider\NachTypProvider;
use App\Tests\Double\SammelDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Zwei Blickwinkel auf den entkoppelten Dienst. Isoliert (mit einem
 * Test-Double statt echtem Dispatcher) prüfen wir, dass er genau ein Ereignis
 * mit den richtigen Daten meldet - ohne einen einzigen Zuhörer zu kennen. Im
 * Zusammenspiel prüfen wir, dass ein einziger bezahle()-Aufruf alle drei
 * unabhängigen Zuhörer erreicht.
 */
final class BestellungBezahlenDienstTest extends TestCase
{
    #[Test]
    public function meldet_genau_ein_bestellung_bezahlt_ereignis(): void
    {
        $dispatcher = new SammelDispatcher();
        $dienst     = new BestellungBezahlenDienst($dispatcher);

        $dienst->bezahle(new Bestellung(7, 'Ada', Geld::ausEuro(49.90)));

        self::assertCount(1, $dispatcher->verteilt);

        $ereignis = $dispatcher->verteilt[0];
        self::assertInstanceOf(BestellungBezahlt::class, $ereignis);
        self::assertSame(7, $ereignis->bestellId);
        self::assertSame('Ada', $ereignis->kunde);
        self::assertSame(4990, $ereignis->betrag->cent);
    }

    #[Test]
    public function gibt_die_bezahlte_bestellung_zurueck(): void
    {
        $dienst = new BestellungBezahlenDienst(new SammelDispatcher());

        $bezahlt = $dienst->bezahle(new Bestellung(7, 'Ada', Geld::ausEuro(49.90)));

        // Dieselbe Identität, nur der Zustand hat sich geändert.
        self::assertSame(7, $bezahlt->id);
        self::assertTrue($bezahlt->bezahlt);
    }

    #[Test]
    public function ein_aufruf_erreicht_alle_drei_zuhoerer(): void
    {
        $mailer    = new Bestaetigungsmailer();
        $statistik = new Umsatzstatistik();
        $treue     = new Treuepunkte();

        $provider = new NachTypProvider();
        $provider->lauscheAuf(BestellungBezahlt::class, $mailer);
        $provider->lauscheAuf(BestellungBezahlt::class, $statistik);
        $provider->lauscheAuf(BestellungBezahlt::class, $treue);

        $dienst = new BestellungBezahlenDienst(new EventDispatcher($provider));

        $dienst->bezahle(new Bestellung(1, 'Ada', Geld::ausEuro(49.90)));

        // Jeder der drei unabhängigen Zuhörer hat auf das eine Ereignis reagiert.
        self::assertCount(1, $mailer->versendet);
        self::assertSame(1, $statistik->anzahl());
        self::assertSame(4990, $statistik->summe()->cent);
        self::assertSame(49, $treue->fuer('Ada'));
    }
}
