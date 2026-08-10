<?php

declare(strict_types=1);

namespace App\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Ein einfacher Listener-Provider: Er ordnet Zuhörer nach dem Ereignistyp
 * (dem Klassennamen). Man meldet einen Zuhörer mit lauscheAuf() für einen
 * Ereignistyp an; getListenersForEvent() liefert dann genau die Zuhörer, die
 * für den konkreten Typ des Ereignisses angemeldet sind - in der Reihenfolge,
 * in der sie angemeldet wurden. Mehr Aufgabenteilung: Der Provider FINDET die
 * Zuhörer, der Dispatcher RUFT sie auf.
 */
final class NachTypProvider implements ListenerProviderInterface
{
    /** @var array<class-string, list<callable>> Zuhörer je Ereignistyp. */
    private array $zuhoerer = [];

    /**
     * Meldet einen Zuhörer für einen Ereignistyp an. Der Typ ist ein
     * Klassenname, meist Ereignis::class.
     *
     * @param class-string $ereignisTyp
     */
    public function lauscheAuf(string $ereignisTyp, callable $zuhoerer): void
    {
        $this->zuhoerer[$ereignisTyp][] = $zuhoerer;
    }

    /**
     * Liefert die Zuhörer für den konkreten Typ des Ereignisses. Gibt es keine,
     * ist das Ergebnis leer - das ist kein Fehler, sondern der Normalfall für
     * Ereignisse, auf die (noch) niemand hört.
     *
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable
    {
        return $this->zuhoerer[$event::class] ?? [];
    }
}
