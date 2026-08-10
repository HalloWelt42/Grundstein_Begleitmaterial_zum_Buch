<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Unsere minimale, unveränderliche Antwort - die Eigenbau-Entsprechung
 * von ResponseInterface aus PSR-7. Statuscode und Grund kommen zu den
 * geerbten Kopfzeilen und dem Rumpf hinzu.
 *
 * Wie bei der Anfrage gilt: Nichts wird nachträglich verändert. withStatus()
 * liefert eine Kopie, und erst send() schreibt am Ende alles hinaus - in
 * der richtigen Reihenfolge (Status, Kopfzeilen, Rumpf), genau wie in
 * Kapitel 36 eingeschärft.
 */
final class Response extends Message
{
    public function __construct(
        private int $status = 200,
        private string $reason = 'OK',
    ) {
        // Sinnvoller Standard: HTML in UTF-8, später überschreibbar.
        $this->headers = ['Content-Type' => 'text/html; charset=utf-8'];
    }

    /** Der HTTP-Statuscode, etwa 200, 401 oder 404. */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /** Der zum Code gehörende Klartext, etwa "OK" oder "Not Found". */
    public function getReasonPhrase(): string
    {
        return $this->reason;
    }

    /** Liefert eine KOPIE der Antwort mit neuem Statuscode und Grund. */
    public function withStatus(int $code, string $reason = ''): static
    {
        $kopie = clone $this;
        $kopie->status = $code;
        $kopie->reason = $reason;

        return $kopie;
    }

    /**
     * Schreibt Status, Kopfzeilen und Rumpf tatsächlich hinaus. Erst der
     * Statuscode, dann die Kopfzeilen, ganz zuletzt der Rumpf - nach der
     * ersten Ausgabe lassen sich keine Kopfzeilen mehr senden.
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
