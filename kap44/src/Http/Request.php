<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Kapselt die Daten einer HTTP-Anfrage. Aufbauend auf dem Request aus
 * Kapitel 36 und dem Mini-Framework aus Kapitel 43, hier um genau eine
 * Sache erweitert: den ungeparsten Rumpf der Anfrage. Denn eine JSON-API
 * bekommt ihre Nutzdaten nicht in $_POST, sondern als roher JSON-Text im
 * Eingabestrom - und den lesen wir an genau einer Stelle ein.
 */
final class Request
{
    /**
     * @param array<string, string> $query    Werte aus dem Abfrageteil ($_GET)
     * @param array<string, string> $body      Werte aus dem Rumpf ($_POST)
     * @param array<string, string> $server    Metadaten der Anfrage ($_SERVER)
     * @param array<string, string> $cookies   gesetzte Cookies ($_COOKIE)
     * @param string                $rawBody   der ungeparste Rumpf (php://input)
     */
    public function __construct(
        private readonly array $query = [],
        private readonly array $body = [],
        private readonly array $server = [],
        private readonly array $cookies = [],
        private readonly string $rawBody = '',
    ) {
    }

    /**
     * Baut ein Request-Objekt aus den echten Superglobals. Das ist die
     * EINZIGE Stelle im Programm, die $_GET und Co. direkt liest - und die
     * einzige, die den Eingabestrom php://input anfasst.
     */
    public static function fromGlobals(): self
    {
        // Der Rumpf einer JSON-Anfrage steht nicht in $_POST, sondern im
        // Eingabestrom php://input. Er wird hier genau einmal gelesen.
        $roh = file_get_contents('php://input');

        return new self($_GET, $_POST, $_SERVER, $_COOKIE, $roh === false ? '' : $roh);
    }

    /** Die HTTP-Methode der Anfrage, etwa "GET", "POST" oder "DELETE". */
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

    /** Der ungeparste Rumpf der Anfrage - bei einer JSON-API der JSON-Text. */
    public function rawBody(): string
    {
        return $this->rawBody;
    }

    /**
     * Liest eine Kopfzeile der Anfrage. PHP legt eingehende Kopfzeilen in
     * $_SERVER unter dem Namen HTTP_... ab: groß geschrieben und mit
     * Unterstrich statt Bindestrich. Aus "Content-Type" wird also
     * HTTP_CONTENT_TYPE.
     */
    public function header(string $name): ?string
    {
        $schluessel = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        return $this->server[$schluessel] ?? null;
    }
}
