<?php

declare(strict_types=1);

namespace App\Adapter\Http;

use App\Application\BestellungBezahlen;
use App\Application\BestellungNichtGefunden;
use App\Application\ZahlungAbgelehnt;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Ein TREIBENDER Adapter (primary). Er übersetzt zwischen der Welt der Anfragen
 * und Statuscodes und dem treibenden Port BestellungBezahlen. Er trifft keine
 * fachliche Entscheidung: Er liest die id, ruft den Anwendungsfall und ordnet
 * dessen fachliche Ausnahmen einem HTTP-Statuscode zu. Zur Vereinfachung
 * arbeiten wir mit Arrays statt echter Anfrage- und Antwort-Objekte.
 */
final class BestellungController
{
    public function __construct(
        private readonly BestellungBezahlen $anwendungsfall,
    ) {}

    /**
     * @param  array<string, mixed>                    $anfrage
     * @return array{status: int, body: array<string, mixed>}
     */
    public function bezahlen(array $anfrage): array
    {
        $id = (int) ($anfrage['id'] ?? 0);

        try {
            $bestellung = $this->anwendungsfall->fuehreAus($id);

            return [
                'status' => 200,
                'body' => [
                    'id'      => $bestellung->id,
                    'kunde'   => $bestellung->kunde,
                    'betrag'  => $bestellung->betrag->alsText(),
                    'bezahlt' => $bestellung->bezahlt,
                ],
            ];
        } catch (BestellungNichtGefunden $fehler) {
            // Unbekannte Ressource wird zu 404 Not Found.
            return ['status' => 404, 'body' => ['fehler' => $fehler->getMessage()]];
        } catch (ZahlungAbgelehnt $fehler) {
            // Abgelehnte Zahlung wird zu 402 Payment Required.
            return ['status' => 402, 'body' => ['fehler' => $fehler->getMessage()]];
        }
    }
}
