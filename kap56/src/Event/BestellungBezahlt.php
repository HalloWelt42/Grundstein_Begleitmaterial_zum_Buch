<?php

declare(strict_types=1);

namespace App\Event;

use App\Domain\Geld;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Ein Ereignis ist eine reine Tatsachenmeldung: "etwas Fachliches ist
 * passiert". Es trägt genau die Daten, die ein Zuhörer braucht, um zu
 * reagieren - hier die Bestell-id, den Kunden und den Betrag -, und sonst
 * nichts. Es ist unveränderlich (final readonly): Kein Zuhörer kann es
 * verändern und damit einem anderen Zuhörer den Boden unter den Füßen
 * wegziehen. Es kennt weder Dispatcher noch Zuhörer.
 */
final readonly class BestellungBezahlt
{
    public function __construct(
        public int $bestellId,
        public string $kunde,
        public Geld $betrag,
    ) {}
}
