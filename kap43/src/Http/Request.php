<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Kapselt die Daten einer HTTP-Anfrage. Unverändert übernommen aus
 * Kapitel 36: Die Superglobals werden EINMAL beim Erzeugen hereingereicht
 * und danach nur noch über getippte Methoden gelesen. Das Mini-Framework
 * in diesem Kapitel baut genau auf diesem Objekt auf.
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

    /** Der angefragte Pfad ohne Abfrageteil, etwa "/kunden/7". */
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
}
