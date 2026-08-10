<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Geld;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Ein zweiter getriebener Port - diesmal zur Außenwelt der Zahlungsanbieter.
 * Die Anwendung sagt nur, WAS sie braucht: einen Betrag belasten. WIE das
 * geschieht (ein externer Dienst, ein Terminal, im Test gar nichts), ist Sache
 * des Adapters. Gelingt die Belastung nicht, wirft der Adapter eine
 * ZahlungAbgelehnt - dieser Fall gehört zum Vertrag und ist Teil der Domäne.
 */
interface ZahlungsPort
{
    /**
     * Belastet den Betrag unter der genannten Referenz.
     *
     * @throws ZahlungAbgelehnt wenn der Anbieter die Zahlung nicht ausführt.
     */
    public function belaste(Geld $betrag, string $referenz): void;
}
