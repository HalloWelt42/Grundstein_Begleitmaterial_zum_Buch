<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\BestellungBezahlt;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Ein Zuhörer, der auf BestellungBezahlt reagiert und eine Bestätigung
 * verschickt. Statt echten Mailversand (der hier nur ablenken würde) sammelt
 * er die Bestätigungen in einem öffentlichen Array - so lässt sich im Test und
 * in der Demo prüfen, dass er reagiert hat. Ein Zuhörer ist einfach ein
 * aufrufbares Objekt: __invoke() macht die Klasse zum callable.
 */
final class Bestaetigungsmailer
{
    /** @var list<string> Versendete Bestätigungen (statt echtem Mailversand). */
    public array $versendet = [];

    public function __invoke(BestellungBezahlt $ereignis): void
    {
        $this->versendet[] = sprintf(
            'Mail an %s: Bestellung #%d über %s ist bezahlt.',
            $ereignis->kunde,
            $ereignis->bestellId,
            $ereignis->betrag->alsText(),
        );
    }
}
