<?php

declare(strict_types=1);

namespace App\Event;

use Psr\EventDispatcher\StoppableEventInterface;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Ein stoppbares Ereignis: Der Kunde soll über den ersten verfügbaren Kanal
 * benachrichtigt werden. Die Kanäle sind der Reihe nach angemeldet; sobald
 * einer zugestellt hat, beendet er die Verbreitung, und die übrigen Kanäle
 * kommen gar nicht mehr zum Zug (erste erfolgreiche Behandlung gewinnt). Die
 * Stopp-Logik steckt im Trait StoppbareVerbreitung.
 */
final class KundeBenachrichtigen implements StoppableEventInterface
{
    use StoppbareVerbreitung;

    public function __construct(
        public readonly string $kunde,
        public readonly string $text,
    ) {}
}
