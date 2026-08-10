<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben (Nachher)
 *
 * Ein getipptes, unveränderliches Datenobjekt (readonly aus Kapitel 13).
 * Es beschafft sich nichts mehr selbst - Code und Zeiten werden ihm beim
 * Bau übergeben. Die Gültigkeitsprüfung ist eine reine Funktion: Der
 * Prüfzeitpunkt kommt als Argument herein, gleiche Eingabe ergibt immer
 * dieselbe Ausgabe, es gibt keine versteckte Zeit und keine
 * Nebenwirkung. Dadurch lässt sie sich ohne jedes Double prüfen.
 */
final class Gutschein
{
    public function __construct(
        public readonly string $code,
        public readonly int $wertCent,
        public readonly DateTimeImmutable $erstelltAm,
        public readonly DateTimeImmutable $gueltigBis,
    ) {}

    public function istGueltigAm(DateTimeImmutable $zeitpunkt): bool
    {
        return $zeitpunkt <= $this->gueltigBis;
    }
}
