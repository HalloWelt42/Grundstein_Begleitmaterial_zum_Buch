<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Bestellung;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Der Anwendungsdienst im Zentrum des Sechsecks. Er setzt den treibenden Port
 * BestellungBezahlen um und stützt sich dabei ausschließlich auf die beiden
 * getriebenen Ports Bestellungen und ZahlungsPort. Beide bekommt er über den
 * Konstruktor gereicht (Dependency Injection). Keine einzige Zeile Technik
 * steht hier - kein new PDO, kein echo, kein Griff nach Uhr oder Netz.
 */
final class BestellungBezahlenDienst implements BestellungBezahlen
{
    public function __construct(
        private readonly Bestellungen $bestellungen,
        private readonly ZahlungsPort $zahlung,
    ) {}

    public function fuehreAus(int $bestellId): Bestellung
    {
        $bestellung = $this->bestellungen->find($bestellId);

        // Sonderfall zuerst: gibt es die Bestellung überhaupt?
        if ($bestellung === null) {
            throw new BestellungNichtGefunden($bestellId);
        }

        // Schon bezahlt? Dann ist nichts zu tun - der Aufruf bleibt gefahrlos
        // wiederholbar und liefert einfach die bezahlte Bestellung zurück.
        if ($bestellung->istOffen() === false) {
            return $bestellung;
        }

        // Über den Port belasten. Wer dahinter steckt, weiß der Dienst nicht -
        // im Fehlerfall wirft der Adapter eine ZahlungAbgelehnt nach oben.
        $this->zahlung->belaste($bestellung->betrag, "Bestellung #{$bestellId}");

        // Erst nach erfolgreicher Zahlung den neuen Zustand festhalten.
        return $this->bestellungen->save($bestellung->alsBezahltMarkiert());
    }
}
