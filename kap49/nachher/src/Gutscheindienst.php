<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben (Nachher)
 *
 * Das System under Test. Der Dienst baut seine Abhängigkeiten nicht mehr
 * selbst, sondern bekommt Uhr und Code-Quelle über den Konstruktor
 * hereingereicht (Dependency Injection, Kapitel 28). Er bleibt bewusst
 * dünn: Er beschafft den Zeitpunkt, holt den Code und setzt daraus einen
 * Gutschein zusammen - mehr nicht. Die eigentliche Entscheidung, ob ein
 * Gutschein gültig ist, liegt als reine Funktion im Gutschein selbst.
 */
final class Gutscheindienst
{
    public function __construct(
        private readonly Clock $uhr,
        private readonly CodeQuelle $codes,
    ) {}

    public function stelleAus(int $wertCent): Gutschein
    {
        $jetzt = $this->uhr->jetzt();

        return new Gutschein(
            $this->codes->naechster(),
            $wertCent,
            $jetzt,
            $jetzt->modify('+30 days'),
        );
    }
}
