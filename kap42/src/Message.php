<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Gemeinsame Basis unserer HTTP-Nachrichten (Anfrage wie Antwort). Sie
 * bündelt die Kopfzeilen und den Rumpf und macht sie UNVERÄNDERLICH:
 * Statt eine vorhandene Nachricht zu ändern, liefern die with...()-Methoden
 * jeweils eine veränderte KOPIE zurück. Das Original bleibt, wie es war.
 *
 * Das ist der Kern der PSR-7-Idee (Kapitel 42) und dieselbe Haltung wie
 * bei readonly aus Kapitel 13: Eine einmal eingetroffene Nachricht ist
 * eine Tatsache, die man nicht mehr heimlich umschreibt.
 */
abstract class Message
{
    /** @var array<string, string> Kopfzeilenname auf seinen Wert. */
    protected array $headers = [];

    protected string $body = '';

    /**
     * Der Wert einer Kopfzeile als String; die Suche ignoriert Groß- und
     * Kleinschreibung, weil HTTP-Kopfzeilennamen unabhängig davon sind.
     * Fehlt die Kopfzeile, kommt ein leerer String zurück.
     */
    public function getHeaderLine(string $name): string
    {
        foreach ($this->headers as $vorhanden => $wert) {
            if (strcasecmp($vorhanden, $name) === 0) {
                return $wert;
            }
        }

        return '';
    }

    /**
     * Liefert eine KOPIE der Nachricht mit gesetzter Kopfzeile. Ein bereits
     * vorhandener gleichnamiger Eintrag (Groß/Klein egal) wird ersetzt.
     * Die aufgerufene Nachricht selbst bleibt unverändert.
     */
    public function withHeader(string $name, string $wert): static
    {
        $kopie = clone $this;

        foreach ($kopie->headers as $vorhanden => $_) {
            if (strcasecmp($vorhanden, $name) === 0) {
                unset($kopie->headers[$vorhanden]);
            }
        }

        $kopie->headers[$name] = $wert;

        return $kopie;
    }

    /** Der Rumpf der Nachricht als String. */
    public function getBody(): string
    {
        return $this->body;
    }

    /** Liefert eine KOPIE der Nachricht mit neuem Rumpf. */
    public function withBody(string $inhalt): static
    {
        $kopie = clone $this;
        $kopie->body = $inhalt;

        return $kopie;
    }
}
