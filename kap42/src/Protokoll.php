<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Ein winziges Protokoll, das Zeilen sammelt. Es steht hier stellvertretend
 * für einen echten Logger (PSR-3, Kapitel 27) und macht in den Beispielen
 * die Reihenfolge sichtbar, in der die Schalen durchlaufen werden.
 */
final class Protokoll
{
    /** @var list<string> */
    private array $zeilen = [];

    public function notiere(string $zeile): void
    {
        $this->zeilen[] = $zeile;
    }

    /** @return list<string> */
    public function zeilen(): array
    {
        return $this->zeilen;
    }
}
