<?php

declare(strict_types=1);

namespace App\Http;

use App\Application\BestellungAufgeben;
use App\Application\BestellungAufgebenDienst;
use DomainException;
use InvalidArgumentException;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Der treibende Adapter (Kapitel 53, 55): der HTTP-Controller. Er trifft keine
 * fachliche Entscheidung. Er liest die rohe Eingabe, baut daraus einen Befehl,
 * ruft den Anwendungsdienst und übersetzt dessen Ergebnis - oder Fehler - in
 * eine Antwort. Zur Vereinfachung arbeiten wir mit Arrays statt echter
 * Request-/Response-Objekte; die Idee bliebe mit ihnen dieselbe.
 */
final class BestellungController
{
    public function __construct(
        private readonly BestellungAufgebenDienst $dienst,
    ) {}

    /**
     * @param  array<string, mixed>                     $eingabe
     * @return array{status: int, body: array<string, mixed>}
     */
    public function aufgeben(array $eingabe): array
    {
        try {
            $bestellung = $this->dienst->fuehreAus(new BestellungAufgeben(
                (string) ($eingabe['kunde'] ?? ''),
                (float) ($eingabe['euro'] ?? 0),
            ));

            return [
                'status' => 201,
                'body' => [
                    'id'     => $bestellung->id,
                    'kunde'  => $bestellung->kunde->wert,
                    'betrag' => $bestellung->betrag->alsText(),
                    'status' => $bestellung->status->value,
                ],
            ];
        } catch (InvalidArgumentException | DomainException $fehler) {
            // Ungültige Eingabe (E-Mail, Betrag) oder verletzte Fachregel
            // wird zu 422 Unprocessable Content. Die Ausnahme kennt keinen
            // Statuscode - erst der Controller ordnet ihr einen zu.
            return [
                'status' => 422,
                'body' => ['fehler' => $fehler->getMessage()],
            ];
        }
    }
}
