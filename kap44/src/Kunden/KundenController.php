<?php

declare(strict_types=1);

namespace Grundstein\Kunden;

use Grundstein\Api\BadRequestException;
use Grundstein\Api\NotFoundException;
use Grundstein\Api\ValidationException;
use Grundstein\Http\Request;
use Grundstein\Http\Response;
use JsonException;

/**
 * Der Controller für die Ressource "Kunde". Jede öffentliche Methode
 * bedient eine Kombination aus HTTP-Methode und Pfad und liefert eine
 * Response. Fehlerfälle wirft der Controller als typisierte Ausnahmen -
 * die JsonErrorMiddleware macht daraus das einheitliche Fehlerformat.
 *
 * Der Controller kennt nur den Vertrag KundenRepository, nicht PDO. Damit
 * bleibt er von der Datenbank entkoppelt und für sich testbar.
 */
final class KundenController
{
    public function __construct(
        private readonly KundenRepository $kunden,
    ) {
    }

    /** GET /kunden - die vollständige Liste, Status 200. */
    public function index(Request $request, array $params): Response
    {
        $liste = array_map(
            fn (Kunde $kunde): array => $this->alsDaten($kunde),
            $this->kunden->findAll(),
        );

        return (new Response())->json(['data' => $liste]);
    }

    /** GET /kunden/{id} - ein einzelner Kunde, Status 200 oder 404. */
    public function show(Request $request, array $params): Response
    {
        $kunde = $this->findeOderFehler((int) $params['id']);

        return (new Response())->json(['data' => $this->alsDaten($kunde)]);
    }

    /**
     * POST /kunden - einen neuen Kunden anlegen. Erfolg: 201 Created mit
     * einem Location-Header, der auf die neue Ressource zeigt.
     */
    public function create(Request $request, array $params): Response
    {
        $eingabe = $this->leseJson($request);
        $kunde = $this->baueKunde($eingabe);

        $gespeichert = $this->kunden->save($kunde);

        return (new Response())
            ->status(201)
            ->header('Location', '/kunden/' . $gespeichert->id)
            ->json(['data' => $this->alsDaten($gespeichert)]);
    }

    /** PUT /kunden/{id} - einen bestehenden Kunden ersetzen, Status 200. */
    public function update(Request $request, array $params): Response
    {
        $id = (int) $params['id'];
        $this->findeOderFehler($id);

        $eingabe = $this->leseJson($request);
        $kunde = $this->baueKunde($eingabe, $id);

        $gespeichert = $this->kunden->save($kunde);

        return (new Response())->json(['data' => $this->alsDaten($gespeichert)]);
    }

    /**
     * DELETE /kunden/{id} - einen Kunden löschen. Erfolg: 204 No Content,
     * eine Antwort ganz ohne Rumpf. Der Content-Type bleibt der einer
     * JSON-API, damit die Schnittstelle auch hier einheitlich antwortet -
     * gelesen wird er bei einer leeren 204 ohnehin nicht.
     */
    public function delete(Request $request, array $params): Response
    {
        $id = (int) $params['id'];
        $this->findeOderFehler($id);

        $this->kunden->delete($id);

        return (new Response())
            ->status(204)
            ->header('Content-Type', 'application/json; charset=utf-8')
            ->body('');
    }

    /**
     * Sucht einen Kunden und wirft eine 404, falls es ihn nicht gibt. So
     * steht die Fehlerbehandlung an einer Stelle statt in jeder Methode.
     */
    private function findeOderFehler(int $id): Kunde
    {
        $kunde = $this->kunden->find($id);
        if ($kunde === null) {
            throw new NotFoundException("Kein Kunde mit der id {$id}.");
        }

        return $kunde;
    }

    /**
     * Liest den JSON-Rumpf der Anfrage. Ein leerer oder ungültiger Rumpf
     * ist ein 400 Bad Request - nicht erst ein Validierungsfehler.
     *
     * @return array<string, mixed>
     */
    private function leseJson(Request $request): array
    {
        $roh = $request->rawBody();
        if ($roh === '') {
            throw new BadRequestException('Der Rumpf der Anfrage ist leer.');
        }

        try {
            // JSON_THROW_ON_ERROR meldet kaputtes JSON als Ausnahme, statt
            // still null zurückzugeben (siehe Kapitel 29).
            $daten = json_decode($roh, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BadRequestException('Der Rumpf ist kein gültiges JSON.');
        }

        if (!is_array($daten)) {
            throw new BadRequestException('Erwartet wird ein JSON-Objekt.');
        }

        return $daten;
    }

    /**
     * Prüft die Eingabefelder und baut daraus einen Kunden. Jeder Fehler
     * wird gesammelt, nicht beim ersten abgebrochen - so bekommt der Client
     * alle Probleme auf einmal (422). Mit id entsteht ein Kunde zum
     * Aktualisieren, ohne id einer zum Anlegen.
     *
     * @param array<string, mixed> $eingabe
     */
    private function baueKunde(array $eingabe, ?int $id = null): Kunde
    {
        $fehler = [];

        $name = trim((string) ($eingabe['name'] ?? ''));
        if ($name === '') {
            $fehler['name'] = 'Der Name darf nicht leer sein.';
        }

        $email = (string) ($eingabe['email'] ?? '');
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $fehler['email'] = 'Bitte eine gültige E-Mail-Adresse angeben.';
        }

        // Der Umsatz ist optional; fehlt er, gilt 0.
        $umsatzCent = 0;
        if (isset($eingabe['umsatzCent'])) {
            $geprueft = filter_var($eingabe['umsatzCent'], FILTER_VALIDATE_INT);
            if ($geprueft === false || $geprueft < 0) {
                $fehler['umsatzCent'] = 'Der Umsatz muss eine ganze Zahl ab 0 sein.';
            } else {
                $umsatzCent = $geprueft;
            }
        }

        if ($fehler !== []) {
            throw new ValidationException('Die Eingabe ist nicht gültig.', $fehler);
        }

        return new Kunde($id, $name, $email, $umsatzCent);
    }

    /**
     * Übersetzt einen Kunden in das Array, das als JSON hinausgeht. Diese
     * eine Stelle bestimmt, welche Felder die API nach außen zeigt.
     *
     * @return array<string, mixed>
     */
    private function alsDaten(Kunde $kunde): array
    {
        return [
            'id' => $kunde->id,
            'name' => $kunde->name,
            'email' => $kunde->email,
            'umsatzCent' => $kunde->umsatzCent,
        ];
    }
}
