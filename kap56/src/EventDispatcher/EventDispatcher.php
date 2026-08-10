<?php

declare(strict_types=1);

namespace App\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Unser eigener PSR-14-Dispatcher. Er ist bewusst winzig: Er fragt den
 * Listener-Provider nach den Zuhörern für dieses Ereignis, ruft sie der Reihe
 * nach auf und gibt das Ereignis am Ende zurück. Die einzige Feinheit ist die
 * Behandlung stoppbarer Ereignisse - nach jedem Zuhörer wird geprüft, ob die
 * Verbreitung enden soll. Der Dispatcher weiß nicht, WIE die Zuhörer gefunden
 * werden; das ist allein Sache des Providers.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly ListenerProviderInterface $provider,
    ) {}

    public function dispatch(object $event): object
    {
        // Ein bereits gestopptes Ereignis wird gar nicht erst verteilt.
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            return $event;
        }

        foreach ($this->provider->getListenersForEvent($event) as $zuhoerer) {
            // Ein Zuhörer ist einfach etwas Aufrufbares (callable).
            $zuhoerer($event);

            // Nach jedem Zuhörer prüfen: Hat er die Verbreitung beendet?
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }

        // PSR-14 verlangt, dass dasselbe Ereignis zurückgegeben wird.
        return $event;
    }
}
