<?php

declare(strict_types=1);

namespace App;

/**
 * Vertrag für einen einfachen Logger. Wer ihn erfüllt, nimmt eine Zeile
 * entgegen und tut damit, was er für richtig hält - schreiben, sammeln
 * oder verwerfen. Der Rest der Anwendung kennt nur diesen Vertrag.
 */
interface Logger
{
    public function notiere(string $zeile): void;
}
