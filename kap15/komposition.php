<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 15: Vererbung, abstrakte Klassen und Interfaces
 *
 * Teil 4: Komposition vor Vererbung. Statt von einer Klasse zu erben,
 * BESITZT ein Objekt ein anderes und delegiert die Arbeit an es. Aus
 * "ist ein" wird "hat ein". Der Auftrag erbt nicht von einem Logger,
 * er hält einen als Eigenschaft und lässt ihn arbeiten.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/**
 * Ein einfacher Logger, der jeder Zeile eine Marke voranstellt.
 */
final class Logger
{
    public function schreibe(string $text): void
    {
        echo "[LOG] {$text}\n";
    }
}

/**
 * Ein Auftrag, der einen Logger benutzt. Er BESITZT den Logger
 * (Komposition), statt selbst einer zu SEIN.
 */
final class Auftrag
{
    public function __construct(
        private readonly Logger $logger,
    ) {}

    public function abschliessen(): void
    {
        $this->logger->schreibe('Auftrag abgeschlossen');
    }
}

// Der Logger wird von außen hineingereicht und ließe sich jederzeit
// gegen einen anderen austauschen - genau das ist der Gewinn.
$auftrag = new Auftrag(new Logger());
$auftrag->abschliessen();
