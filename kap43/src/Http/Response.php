<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Kapselt eine HTTP-Antwort: Statuscode, Header und Rumpf. Übernommen aus
 * Kapitel 36 und um json() ergänzt, das im Mini-Framework praktisch ist.
 * Nichts wird sofort ausgegeben - erst send() schreibt alles in einem
 * Rutsch hinaus. Die Setter geben $this zurück, sodass sich Aufrufe
 * verketten lassen.
 */
final class Response
{
    /** @var array<string, string> Name des Headers auf seinen Wert. */
    private array $headers = [];

    private int $status = 200;

    private string $body = '';

    public function __construct()
    {
        // Sinnvoller Standard: HTML in UTF-8. Kann später überschrieben werden.
        $this->headers['Content-Type'] = 'text/html; charset=utf-8';
    }

    /** Setzt den HTTP-Statuscode, etwa 200, 404 oder 405. */
    public function status(int $code): self
    {
        $this->status = $code;

        return $this;
    }

    /** Setzt einen Header. Ein gleichnamiger Header wird überschrieben. */
    public function header(string $name, string $wert): self
    {
        $this->headers[$name] = $wert;

        return $this;
    }

    /** Legt den Rumpf der Antwort fest. */
    public function body(string $inhalt): self
    {
        $this->body = $inhalt;

        return $this;
    }

    /**
     * Antwortet mit JSON: passender Content-Type und das kodierte Array
     * als Rumpf. Umlaute und Schrägstriche bleiben lesbar. JSON_THROW_ON_ERROR
     * sorgt dafür, dass ein Kodierfehler eine saubere JsonException wirft,
     * statt still false zu liefern und danach an der getippten Eigenschaft
     * $body einen unklaren TypeError auszulösen.
     *
     * @param array<string, mixed> $daten
     *
     * @throws \JsonException wenn sich die Daten nicht kodieren lassen
     */
    public function json(array $daten): self
    {
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';
        $this->body = json_encode(
            $daten,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );

        return $this;
    }

    /**
     * Schreibt Status, Header und Rumpf tatsächlich hinaus. Reihenfolge
     * ist Pflicht: erst der Statuscode, dann die Header, ganz zuletzt der
     * Rumpf - denn nach der ersten Ausgabe lassen sich keine Header mehr
     * senden.
     */
    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $wert) {
            header("{$name}: {$wert}");
        }

        echo $this->body;
    }
}
