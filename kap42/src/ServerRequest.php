<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Unsere minimale, unveränderliche Server-Anfrage - die schlanke
 * Eigenbau-Entsprechung von ServerRequestInterface aus PSR-7. Sie erbt
 * die Kopfzeilen und den Rumpf aus Message und ergänzt Methode, Pfad,
 * Abfrageparameter und die ATTRIBUTE.
 *
 * Attribute sind der Weg, auf dem eine Middleware Erkenntnisse nach innen
 * weiterreicht - etwa den erkannten Benutzer. Weil das Objekt
 * unveränderlich ist, geschieht das mit withAttribute(), das eine Kopie
 * liefert.
 */
final class ServerRequest extends Message
{
    /**
     * @param array<string, string> $query      Werte aus dem Abfrageteil
     * @param array<string, string> $headers    Kopfzeilen der Anfrage
     * @param array<string, mixed>  $attributes von Middleware angehängte Werte
     */
    public function __construct(
        private readonly string $method = 'GET',
        private readonly string $path = '/',
        private readonly array $query = [],
        array $headers = [],
        private array $attributes = [],
    ) {
        $this->headers = $headers;
    }

    /**
     * Baut eine Anfrage aus den echten Superglobals - die einzige Stelle,
     * die $_SERVER und $_GET berührt (wie in Kapitel 36).
     */
    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $ziel = $_SERVER['REQUEST_URI'] ?? '/';
        $path = explode('?', $ziel, 2)[0];

        // Kopfzeilen stehen in $_SERVER mit dem Präfix HTTP_ und in
        // Großbuchstaben; wir bauen daraus wieder "X-Token" und Co.
        $headers = [];
        foreach ($_SERVER as $schluessel => $wert) {
            if (str_starts_with($schluessel, 'HTTP_')) {
                $name = str_replace('_', '-', substr($schluessel, 5));
                $name = ucwords(strtolower($name), '-');
                $headers[$name] = (string) $wert;
            }
        }

        return new self($method, $path, $_GET, $headers);
    }

    /** Die HTTP-Methode, etwa "GET" oder "POST". */
    public function getMethod(): string
    {
        return $this->method;
    }

    /** Der angefragte Pfad ohne Abfrageteil, etwa "/gruss". */
    public function getPath(): string
    {
        return $this->path;
    }

    /** @return array<string, string> alle Werte aus dem Abfrageteil */
    public function getQueryParams(): array
    {
        return $this->query;
    }

    /** Ein von Middleware angehängtes Attribut oder der Standardwert. */
    public function getAttribute(string $name, mixed $standard = null): mixed
    {
        return $this->attributes[$name] ?? $standard;
    }

    /**
     * Liefert eine KOPIE der Anfrage mit einem gesetzten Attribut. So
     * reicht eine Middleware Erkenntnisse nach innen weiter, ohne das
     * Original zu verändern. Der Rückgabetyp static hält dabei den
     * konkreten Typ - dieselbe Regel wie bei withHeader() in Message.
     */
    public function withAttribute(string $name, mixed $wert): static
    {
        $kopie = clone $this;
        $kopie->attributes[$name] = $wert;

        return $kopie;
    }
}
