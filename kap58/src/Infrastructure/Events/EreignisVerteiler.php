<?php

declare(strict_types=1);

namespace App\Infrastructure\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Der Ereignis-Verteiler aus PSR-14 (Kapitel 56). Er holt sich vom Provider
 * die passenden Zuhörer und ruft sie der Reihe nach mit dem Ereignis auf. Ein
 * abbrechbares Ereignis (StoppableEventInterface) kann die Kette vorzeitig
 * beenden. Der Verteiler kennt die einzelnen Zuhörer nicht - genau das
 * entkoppelt Auslöser und Reaktion.
 */
final class EreignisVerteiler implements EventDispatcherInterface
{
    public function __construct(
        private readonly ListenerProviderInterface $provider,
    ) {}

    public function dispatch(object $event): object
    {
        foreach ($this->provider->getListenersForEvent($event) as $zuhoerer) {
            // Abbruch, sobald ein abbrechbares Ereignis gestoppt wurde.
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            $zuhoerer($event);
        }

        // PSR-14: Der Verteiler gibt dasselbe Ereignis zurück.
        return $event;
    }
}
