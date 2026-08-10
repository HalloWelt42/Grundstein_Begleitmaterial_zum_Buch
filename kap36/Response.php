<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Kapselt eine HTTP-Antwort: Statuscode, Header und Rumpf. Nichts wird
 * sofort ausgegeben - erst send() schreibt alles in einem Rutsch hinaus.
 * So bleibt bis zum Schluss änderbar, was die Antwort enthält, und der
 * Ablauf ist leicht nachzuvollziehen.
 *
 * Die Setter geben jeweils $this zurück, damit sich Aufrufe verketten
 * lassen: $antwort->status(200)->header(...)->body(...).
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

    /** Setzt den HTTP-Statuscode, etwa 200, 404 oder 400. */
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
     * Bequeme Weiterleitung: Status 302 und ein Location-Header auf das
     * Ziel. Der Rumpf bleibt leer, weil der Browser sofort weiterzieht.
     */
    public function redirect(string $ziel): self
    {
        $this->status = 302;
        $this->headers['Location'] = $ziel;
        $this->body = '';

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
