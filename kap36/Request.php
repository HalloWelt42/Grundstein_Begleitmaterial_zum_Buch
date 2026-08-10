<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Kapselt die Daten einer HTTP-Anfrage. Die Superglobals werden EINMAL
 * beim Erzeugen hereingereicht und danach nur noch über getippte
 * Methoden gelesen - nirgends sonst im Programm steht $_GET oder $_POST.
 * Das macht den Code testbar: Ein Test baut ein Request-Objekt aus
 * eigenen Arrays, ganz ohne echten Webserver.
 *
 * Ein Vorgriff auf PSR-7 (Kapitel 42): Dort wird die Anfrage zu einem
 * standardisierten Objekt mit festgelegten Methoden. Hier bauen wir die
 * einfache Eigenbau-Variante, um das Prinzip zu verstehen.
 */
final class Request
{
    /**
     * @param array<string, string> $query    Werte aus dem Abfrageteil ($_GET)
     * @param array<string, string> $body      Werte aus dem Rumpf ($_POST)
     * @param array<string, string> $server    Metadaten der Anfrage ($_SERVER)
     * @param array<string, string> $cookies   gesetzte Cookies ($_COOKIE)
     */
    public function __construct(
        private readonly array $query = [],
        private readonly array $body = [],
        private readonly array $server = [],
        private readonly array $cookies = [],
    ) {
    }

    /**
     * Baut ein Request-Objekt aus den echten Superglobals. Das ist die
     * EINZIGE Stelle im Programm, die $_GET und Co. direkt liest.
     */
    public static function fromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER, $_COOKIE);
    }

    /** Die HTTP-Methode der Anfrage, etwa "GET" oder "POST". */
    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    /** Der angefragte Pfad ohne Abfrageteil, etwa "/suche". */
    public function path(): string
    {
        $ziel = $this->server['REQUEST_URI'] ?? '/';

        // Alles ab dem ersten Fragezeichen abschneiden.
        return explode('?', $ziel, 2)[0];
    }

    /** Ein roher Wert aus dem Abfrageteil oder der Standardwert. */
    public function query(string $name, ?string $standard = null): ?string
    {
        return $this->query[$name] ?? $standard;
    }

    /** Ein roher Wert aus dem Rumpf oder der Standardwert. */
    public function body(string $name, ?string $standard = null): ?string
    {
        return $this->body[$name] ?? $standard;
    }

    /** Ein Cookie-Wert oder der Standardwert. */
    public function cookie(string $name, ?string $standard = null): ?string
    {
        return $this->cookies[$name] ?? $standard;
    }

    /**
     * Liest einen Abfragewert als geprüften ganzzahligen Wert. Fehlt der
     * Wert oder ist er keine Ganzzahl, kommt null zurück - der Aufrufer
     * entscheidet dann selbst, wie er darauf reagiert.
     */
    public function queryInt(string $name): ?int
    {
        $roh = $this->query[$name] ?? null;
        if ($roh === null) {
            return null;
        }

        // filter_var arbeitet auf dem gekapselten Wert, nicht auf $_GET.
        $geprueft = filter_var($roh, FILTER_VALIDATE_INT);

        return $geprueft === false ? null : $geprueft;
    }
}
