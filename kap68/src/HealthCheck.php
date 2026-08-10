<?php

declare(strict_types=1);

namespace App;

/**
 * Eine Gesundheitsprüfung für den Betrieb. Sie beantwortet knapp die
 * Frage, ob diese Instanz bereit ist, Anfragen zu bearbeiten - genau das,
 * was ein Reverse-Proxy oder ein Orchestrierer vor dem Umschalten wissen
 * muss. Jede einzelne Prüfung ist eine Closure, die true (gesund) oder
 * false (krank) zurückgibt; so bleibt der Katalog leicht erweiterbar und
 * im Test vollständig steuerbar.
 */
final class HealthCheck
{
    /**
     * @param array<string, callable(): bool> $pruefungen
     */
    public function __construct(
        private readonly string $version,
        private readonly array $pruefungen,
    ) {
    }

    /**
     * Führt alle Prüfungen aus und fasst das Ergebnis zusammen. Der
     * Gesamtstatus ist nur dann "ok", wenn jede einzelne Prüfung besteht.
     *
     * @return array{status: string, version: string, checks: array<string, string>}
     */
    public function ausfuehren(): array
    {
        $checks = [];
        $gesund = true;

        foreach ($this->pruefungen as $name => $pruefung) {
            $ok = $pruefung();
            $checks[$name] = $ok ? 'ok' : 'fehler';
            $gesund = $gesund && $ok;
        }

        return [
            'status'  => $gesund ? 'ok' : 'fehler',
            'version' => $this->version,
            'checks'  => $checks,
        ];
    }
}
