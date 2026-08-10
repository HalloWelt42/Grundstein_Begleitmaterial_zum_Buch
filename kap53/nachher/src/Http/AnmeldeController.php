<?php

declare(strict_types=1);

namespace App\Http;

use App\Application\AnmeldeService;
use App\Application\Anmeldebefehl;
use App\Application\BereitsAngemeldet;
use App\Application\UngueltigeEmail;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Die Präsentationsschicht. Der Controller nimmt die HTTP-Eingabe
 * entgegen, übersetzt sie in einen Befehl, delegiert an den
 * Anwendungsdienst und übersetzt dessen Ergebnis (oder Fehler) zurück in
 * eine HTTP-Antwort. Er rechnet nichts selbst, prüft keine Fachregel und
 * sieht keine Datenbank - er vermittelt nur zwischen HTTP und Anwendung.
 */
final class AnmeldeController
{
    public function __construct(
        private readonly AnmeldeService $service,
    ) {}

    /**
     * @param array<string, string> $eingabe Rohdaten aus dem Formular ($_POST)
     */
    public function anmelden(array $eingabe): HttpAntwort
    {
        // HTTP-Eingabe in ein getipptes Eingabeobjekt übersetzen.
        $befehl = new Anmeldebefehl($eingabe['email'] ?? '');

        try {
            $abonnent = $this->service->meldeAn($befehl);
        } catch (UngueltigeEmail) {
            return new HttpAntwort(422, 'Bitte eine gültige E-Mail-Adresse angeben.');
        } catch (BereitsAngemeldet) {
            return new HttpAntwort(409, 'Diese Adresse ist bereits angemeldet.');
        }

        return new HttpAntwort(
            201,
            "Danke! {$abonnent->email} ist jetzt angemeldet.",
        );
    }
}
