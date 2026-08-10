<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\KundeBenachrichtigen;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Der zweite, nachrangige Kanal. Er würde ebenso zustellen - aber weil der
 * Push-Kanal vor ihm angemeldet ist und die Verbreitung beendet, kommt er im
 * Regelfall gar nicht mehr an die Reihe. Er springt nur ein, wenn der Push-
 * Kanal nicht angemeldet ist oder die Verbreitung nicht gestoppt hat.
 */
final class SmsKanal
{
    /** @var list<string> Was dieser Kanal zugestellt hat. */
    public array $zugestellt = [];

    public function __invoke(KundeBenachrichtigen $ereignis): void
    {
        $this->zugestellt[] = "SMS an {$ereignis->kunde}: {$ereignis->text}";

        $ereignis->stoppeVerbreitung();
    }
}
