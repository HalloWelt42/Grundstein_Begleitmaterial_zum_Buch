<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 4: Die Phasen eines Laufs sichtbar gemacht.
 *
 * PHP arbeitet ein Skript in Phasen ab: Start, Ausführung, Beenden.
 * Eine mit register_shutdown_function() angemeldete Funktion läuft
 * garantiert ganz am Ende - in der Beenden-Phase, nachdem der letzte
 * gewöhnliche Befehl abgearbeitet ist.
 */

// Wird erst in der Beenden-Phase aufgerufen, egal wo im Skript sie steht.
register_shutdown_function(static function (): void {
    echo '[Beenden]     Aufräumen - danach ist der Lauf vorbei.' . PHP_EOL;
});

echo '[Start]       Der Interpreter hat das Skript geladen.' . PHP_EOL;
echo '[Ausführung] Das Skript tut seine eigentliche Arbeit.' . PHP_EOL;

// Kein explizites Ende nötig: PHP schließt den Lauf selbst ab und
// ruft dabei die oben angemeldete Funktion auf.
