<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Bestellung;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein Ereignis (Kapitel 56): eine schlichte, unveränderliche Tatsache in der
 * Vergangenheitsform - eine Bestellung wurde aufgegeben. Der Anwendungsdienst
 * sendet sie aus, ohne zu wissen, wer darauf reagiert. Ein Ereignis ist ein
 * beliebiges Objekt; erst der Verteiler und der Vertrag PSR-14 machen daraus
 * eine entkoppelte Nachricht.
 */
final readonly class BestellungAufgegeben
{
    public function __construct(
        public Bestellung $bestellung,
    ) {}
}
