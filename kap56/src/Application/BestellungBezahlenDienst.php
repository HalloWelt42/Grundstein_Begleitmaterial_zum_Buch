<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Bestellung;
use App\Event\BestellungBezahlt;
use Psr\EventDispatcher\EventDispatcherInterface;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Der Anwendungsdienst nach der Entkopplung. Er tut seine Kernarbeit - die
 * Bestellung fachlich als bezahlt fortschreiben - und meldet danach nur EIN
 * Ereignis: "Bestellung bezahlt". Wer darauf reagiert (Mailer, Statistik,
 * Treuepunkte, ...), weiß er nicht und muss es nicht wissen. Seine einzige
 * neue Abhängigkeit ist der Dispatcher hinter dem PSR-14-Interface - nicht die
 * einzelnen Zuhörer. So bleibt der Dienst schlank, egal wie viele Nebenwirkungen
 * später dazukommen.
 *
 * Hinweis zum Buch: In Kapitel 55 orchestrierte dieser Dienst zwei getriebene
 * Ports (Datenbank und Zahlung). Kapitel 56 verkürzt ihn bewusst auf den Kern
 * der Zustandsfortschreibung, um die Ereignis-Idee ungestört freizulegen; im
 * Beispielprojekt (Kapitel 58) kehren die Ports neben den Ereignissen zurück.
 */
final class BestellungBezahlenDienst
{
    public function __construct(
        private readonly EventDispatcherInterface $ereignisse,
    ) {}

    public function bezahle(Bestellung $bestellung): Bestellung
    {
        // Kernarbeit des Anwendungsfalls: den Zustand fachlich fortschreiben.
        $bezahlt = $bestellung->alsBezahltMarkiert();

        // Danach genau ein Ereignis melden. Der Dienst kennt keinen einzigen
        // seiner Zuhörer - er spricht nur den Dispatcher an.
        $this->ereignisse->dispatch(new BestellungBezahlt(
            $bezahlt->id ?? 0,
            $bezahlt->kunde,
            $bezahlt->betrag,
        ));

        return $bezahlt;
    }
}
