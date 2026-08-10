<?php

declare(strict_types=1);

namespace App\Tests\Double;

use Psr\EventDispatcher\EventDispatcherInterface;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Ein Test-Double für den Dispatcher: Es ruft keine Zuhörer auf, sondern merkt
 * sich nur, welche Ereignisse verteilt wurden. So kann ein Test den
 * Anwendungsdienst isoliert prüfen - meldet er das richtige Ereignis? - ganz
 * ohne echte Zuhörer, echten Provider oder echten Dispatcher.
 */
final class SammelDispatcher implements EventDispatcherInterface
{
    /** @var list<object> Alle verteilten Ereignisse, in Reihenfolge. */
    public array $verteilt = [];

    public function dispatch(object $event): object
    {
        $this->verteilt[] = $event;

        return $event;
    }
}
