<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\BestellungAufgeben;
use App\Application\BestellungAufgebenDienst;
use App\Application\BestellungAufgegeben;
use App\Infrastructure\Events\EreignisVerteiler;
use App\Infrastructure\Events\ListenerRegister;
use App\Infrastructure\Persistence\InMemoryBestellungen;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Der Anwendungsfall-Test. Er baut den Dienst mit dem In-Memory-Adapter (kein
 * SQL, kein Server) und einer echten Ereignis-Infrastruktur zusammen. Ein als
 * Closure angemeldeter Zuhörer belegt, dass genau ein Ereignis ausgelöst wurde
 * und die richtige Bestellung trägt - so prüfen wir die Entkopplung über
 * Ereignisse (Kapitel 55, 56) ausführbar.
 */
final class BestellungAufgebenDienstTest extends TestCase
{
    #[Test]
    public function legt_eine_bestellung_an_und_loest_genau_ein_ereignis_aus(): void
    {
        $bestellungen = new InMemoryBestellungen();

        // Ereignis-Infrastruktur mit einem sammelnden Zuhörer.
        $empfangen = [];
        $register  = new ListenerRegister();
        $register->hoerAuf(
            BestellungAufgegeben::class,
            static function (BestellungAufgegeben $e) use (&$empfangen): void {
                $empfangen[] = $e;
            },
        );

        $dienst = new BestellungAufgebenDienst(
            $bestellungen,
            new EreignisVerteiler($register),
        );

        $bestellung = $dienst->fuehreAus(new BestellungAufgeben('Ada@Example.org', 49.90));

        // Ergebnis: die Bestellung ist gespeichert und trägt eine id ...
        self::assertSame(1, $bestellung->id);
        // ... die Adresse wurde normalisiert (klein geschrieben) ...
        self::assertSame('ada@example.org', $bestellung->kunde->wert);
        self::assertCount(1, $bestellungen->alle());

        // ... und genau ein Ereignis mit der richtigen Bestellung wurde ausgelöst.
        self::assertCount(1, $empfangen);
        self::assertSame($bestellung, $empfangen[0]->bestellung);
    }

    #[Test]
    public function eine_ungueltige_adresse_verhindert_bestellung_und_ereignis(): void
    {
        $bestellungen = new InMemoryBestellungen();

        $empfangen = [];
        $register  = new ListenerRegister();
        $register->hoerAuf(
            BestellungAufgegeben::class,
            static function (BestellungAufgegeben $e) use (&$empfangen): void {
                $empfangen[] = $e;
            },
        );

        $dienst = new BestellungAufgebenDienst(
            $bestellungen,
            new EreignisVerteiler($register),
        );

        try {
            $dienst->fuehreAus(new BestellungAufgeben('kein-at-zeichen', 10.0));
            self::fail('Erwartet wurde eine InvalidArgumentException.');
        } catch (InvalidArgumentException) {
            // Erwartet: die ungültige Adresse scheitert am Wertobjekt.
        }

        // Nichts wurde gespeichert, kein Ereignis ausgelöst.
        self::assertCount(0, $bestellungen->alle());
        self::assertCount(0, $empfangen);
    }
}
