<?php

declare(strict_types=1);

namespace App\Event;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Ein wiederverwendbarer Baustein für stoppbare Ereignisse. Wer diesen Trait
 * einbindet und StoppableEventInterface umsetzt, bekommt die kleine
 * Zustandsmaschine geschenkt: Ein Zuhörer kann die Verbreitung beenden, und
 * der Dispatcher fragt danach ab. Weil ein stoppbares Ereignis diesen einen
 * veränderlichen Zustand trägt, ist es naturgemäß nicht mehr vollständig
 * unveränderlich - anders als eine reine Tatsachenmeldung wie BestellungBezahlt.
 */
trait StoppbareVerbreitung
{
    private bool $verbreitungGestoppt = false;

    public function isPropagationStopped(): bool
    {
        return $this->verbreitungGestoppt;
    }

    public function stoppeVerbreitung(): void
    {
        $this->verbreitungGestoppt = true;
    }
}
