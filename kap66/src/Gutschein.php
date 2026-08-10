<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Ein Gutschein als unveränderliches Wertobjekt (Kapitel 54): Er trägt
 * seinen Wert und zwei Zeitpunkte - Ausstellung und Ablauf -, beide als
 * DateTimeImmutable in UTC. Weil DateTimeImmutable unveränderlich ist,
 * kann kein Aufrufer die Zeiten nachträglich verbiegen; das Objekt bleibt
 * über seine ganze Lebensdauer stimmig.
 *
 * Die Gültigkeitsprüfung ist eine reine Funktion (Kapitel 49): Der
 * Prüfzeitpunkt kommt als Argument herein, es gibt keine versteckte Uhr
 * und keine Nebenwirkung. Dadurch ist sie ohne jedes Double prüfbar.
 */
final class Gutschein
{
    public function __construct(
        public readonly int $wertCent,
        public readonly DateTimeImmutable $ausgestelltAm,
        public readonly DateTimeImmutable $gueltigBis,
    ) {
    }

    public function istGueltigAm(DateTimeImmutable $zeitpunkt): bool
    {
        // DateTimeImmutable-Objekte lassen sich direkt vergleichen - der
        // Vergleich geht über den echten Augenblick, nicht über Strings.
        return $zeitpunkt <= $this->gueltigBis;
    }
}
