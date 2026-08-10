<?php

declare(strict_types=1);

namespace Grundstein\Mini;

use Grundstein\Http\Request;
use Grundstein\Http\Response;

/**
 * Ein Handler als aufrufbare Klasse. Dank __invoke lässt sich das Objekt
 * wie eine Funktion aufrufen - der Dispatcher merkt keinen Unterschied zu
 * einer Closure. Der Vorteil: Ein solcher Handler kann Abhängigkeiten im
 * Konstruktor bekommen (hier ein simples Array, später ein Repository aus
 * Kapitel 32).
 */
final class ShowKundeHandler
{
    /** @var array<int, array{name: string, ort: string}> */
    private array $kunden = [
        1 => ['name' => 'Ada Lovelace', 'ort' => 'London'],
        2 => ['name' => 'Grace Hopper', 'ort' => 'New York'],
    ];

    /**
     * @param array<string, string> $params die Pfadparameter, hier "id"
     */
    public function __invoke(Request $request, array $params): Response
    {
        $id = (int) $params['id'];

        // Route-404 (Pfad unbekannt) und Fach-404 (Kunde unbekannt) sind
        // zwei Paar Schuhe. Diese hier meldet der Handler selbst.
        if (!isset($this->kunden[$id])) {
            return (new Response())
                ->status(404)
                ->json(['fehler' => "Kein Kunde mit id {$id}"]);
        }

        $kunde = $this->kunden[$id];

        // Der Client bekommt JSON. Bei HTML müsste jeder Wert durch
        // htmlspecialchars - JSON entschärft die Sonderzeichen selbst.
        return (new Response())->json([
            'id' => $id,
            'name' => $kunde['name'],
            'ort' => $kunde['ort'],
        ]);
    }
}
