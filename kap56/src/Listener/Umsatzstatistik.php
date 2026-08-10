<?php

declare(strict_types=1);

namespace App\Listener;

use App\Domain\Geld;
use App\Event\BestellungBezahlt;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Ein zweiter, völlig unabhängiger Zuhörer auf dasselbe Ereignis. Er zählt die
 * bezahlten Bestellungen und summiert den Umsatz. Er weiß nichts vom Mailer
 * und nichts von den Treuepunkten - jeder Zuhörer kümmert sich um genau eine
 * Nebenwirkung.
 */
final class Umsatzstatistik
{
    private int $anzahl = 0;

    private int $summeCent = 0;

    public function __invoke(BestellungBezahlt $ereignis): void
    {
        $this->anzahl++;
        $this->summeCent += $ereignis->betrag->cent;
    }

    public function anzahl(): int
    {
        return $this->anzahl;
    }

    public function summe(): Geld
    {
        return new Geld($this->summeCent);
    }
}
