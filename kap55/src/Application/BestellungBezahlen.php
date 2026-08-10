<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Bestellung;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Der TREIBENDE Port (primary port). Er beschreibt einen Anwendungsfall aus
 * Sicht der Aufrufer: "Bezahle die Bestellung mit dieser id." Treibende
 * Adapter - ein HTTP-Controller, ein CLI-Befehl, ein Test - rufen die Anwendung
 * ausschließlich über diesen Vertrag auf und wissen nichts von ihrem Innenleben.
 */
interface BestellungBezahlen
{
    /**
     * Führt den Anwendungsfall aus und gibt die bezahlte Bestellung zurück.
     *
     * @throws BestellungNichtGefunden wenn es die id nicht gibt.
     * @throws ZahlungAbgelehnt        wenn der Zahlungsanbieter ablehnt.
     */
    public function fuehreAus(int $bestellId): Bestellung;
}
