<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Bestellung;
use App\Domain\EmailAdresse;
use App\Domain\Geldbetrag;
use Psr\EventDispatcher\EventDispatcherInterface;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Der Anwendungsdienst - das Herzstück des Anwendungsfalls "Bestellung
 * aufgeben". Er orchestriert nur: Er übersetzt den Befehl in Domänen-Objekte,
 * lässt speichern und meldet die Tatsache als Ereignis. Seine beiden
 * Abhängigkeiten - der Repository-Port und der Ereignis-Verteiler (PSR-14) -
 * kommen über den Konstruktor herein. Keine Zeile Technik steht hier, und der
 * Dienst weiß nicht, wer auf sein Ereignis lauscht. Genau das hält ihn schlank.
 */
final class BestellungAufgebenDienst
{
    public function __construct(
        private readonly Bestellungen $bestellungen,
        private readonly EventDispatcherInterface $ereignisse,
    ) {}

    public function fuehreAus(BestellungAufgeben $befehl): Bestellung
    {
        // Rohe Eingabe in gültige Wertobjekte übersetzen. Schlägt eine
        // Prüfung fehl, entsteht gar keine Bestellung - der Fehler fliegt nach
        // oben und wird an der Http-Grenze in einen Statuscode übersetzt.
        $kunde  = new EmailAdresse($befehl->kunde);
        $betrag = Geldbetrag::ausEuro($befehl->euro);

        // Die Entity setzt ihre Invariante selbst durch (Betrag größer null).
        $bestellung = Bestellung::neu($kunde, $betrag);

        // Über den Port speichern. Ob dahinter eine Datenbank oder ein Array
        // steckt, weiß der Dienst nicht - und muss es nicht wissen.
        $gespeichert = $this->bestellungen->save($bestellung);

        // Die Tatsache aussenden. Wer darauf reagiert - eine Bestätigung
        // verschickt, eine Statistik führt -, entscheidet die Verdrahtung an
        // der Kompositionswurzel, nicht dieser Dienst.
        $this->ereignisse->dispatch(new BestellungAufgegeben($gespeichert));

        return $gespeichert;
    }
}
