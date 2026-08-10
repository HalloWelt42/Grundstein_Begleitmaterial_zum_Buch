<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\KundeBenachrichtigen;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Ein Zuhörer auf das stoppbare Ereignis KundeBenachrichtigen. Der Push-Kanal
 * hat den Vorrang: Stellt er zu, beendet er die Verbreitung, und kein weiterer
 * Kanal versucht es noch (erste erfolgreiche Behandlung gewinnt).
 */
final class PushKanal
{
    /** @var list<string> Was dieser Kanal zugestellt hat. */
    public array $zugestellt = [];

    public function __invoke(KundeBenachrichtigen $ereignis): void
    {
        $this->zugestellt[] = "Push an {$ereignis->kunde}: {$ereignis->text}";

        // Erfolgreich zugestellt - die übrigen Kanäle können sich das sparen.
        $ereignis->stoppeVerbreitung();
    }
}
