<?php

declare(strict_types=1);

namespace App\Infrastructure\Events;

use Psr\EventDispatcher\ListenerProviderInterface;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Der Listener-Provider aus PSR-14 (Kapitel 56). Er beantwortet die eine
 * Frage: Welche Zuhörer gehören zu diesem Ereignis? Zuhörer werden unter einem
 * Ereignis-Typ angemeldet; beim Verteilen bekommt jeder Zuhörer das Ereignis,
 * dessen Typ er abonniert hat - Typprüfung per instanceof, damit auch Oberklassen
 * und Interfaces greifen.
 */
final class ListenerRegister implements ListenerProviderInterface
{
    /** @var array<class-string, list<callable>> Ereignis-Typ -> Zuhörer. */
    private array $zuhoerer = [];

    /**
     * Meldet einen Zuhörer für einen Ereignis-Typ an.
     *
     * @param class-string $ereignisTyp
     */
    public function hoerAuf(string $ereignisTyp, callable $zuhoerer): void
    {
        $this->zuhoerer[$ereignisTyp][] = $zuhoerer;
    }

    /**
     * PSR-14: Liefert alle Zuhörer, die zum Typ des Ereignisses passen.
     *
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->zuhoerer as $typ => $liste) {
            if ($event instanceof $typ) {
                yield from $liste;
            }
        }
    }
}
