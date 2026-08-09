<?php

declare(strict_types=1);

/**
 * Mit __invoke wird ein Objekt selbst aufrufbar - es verhält sich wie eine
 * Funktion, trägt aber Zustand mit sich. Solche Objekte heißen oft
 * "aufrufbare Objekte" und lassen sich überall als Callback einsetzen.
 */
final class Multiplikator
{
    public function __construct(
        private readonly int $faktor,
    ) {}

    /**
     * Wird ausgeführt, wenn man das Objekt wie eine Funktion aufruft.
     */
    public function __invoke(int $zahl): int
    {
        return $zahl * $this->faktor;
    }
}

$verdoppeln = new Multiplikator(2);

// Das Objekt direkt wie eine Funktion aufrufen.
echo $verdoppeln(21) . "\n";

// PHP erkennt es als aufrufbar.
var_dump(is_callable($verdoppeln));

// Deshalb taugt es überall als Callback, hier bei array_map.
$verdreifachen = new Multiplikator(3);
$ergebnis = array_map($verdreifachen, [1, 2, 3, 4]);
echo implode(', ', $ergebnis) . "\n";
