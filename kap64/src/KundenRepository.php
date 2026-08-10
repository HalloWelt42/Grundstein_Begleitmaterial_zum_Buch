<?php

declare(strict_types=1);

namespace App;

/**
 * Baut einen Kunden aus seinen Stammdaten und hängt ihm die teure
 * Bestellhistorie als Lazy-Proxy an. Wer nur den Namen anzeigt, löst die
 * Abfrage nie aus - genau das Muster, das ein ORM für Beziehungen nutzt.
 */
final class KundenRepository
{
    public function finde(int $id, string $name): Kunde
    {
        $historie = LazyFabrik::proxy(
            Bestellhistorie::class,
            // Erst wenn jemand die Historie berührt, wird sie geladen.
            static fn (Bestellhistorie $platzhalter): Bestellhistorie
                => new Bestellhistorie($id),
        );

        return new Kunde($id, $name, $historie);
    }
}
