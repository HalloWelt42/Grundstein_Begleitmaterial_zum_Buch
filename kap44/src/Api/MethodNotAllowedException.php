<?php

declare(strict_types=1);

namespace Grundstein\Api;

/**
 * Der Pfad ist bekannt, aber nicht mit dieser Methode. Ein DELETE auf eine
 * Ressource, die nur GET anbietet, landet hier. Antwort: 405 Method Not
 * Allowed - und ein Allow-Header, der die erlaubten Methoden nennt, wie es
 * der HTTP-Standard vorschreibt.
 */
final class MethodNotAllowedException extends ApiException
{
    /**
     * @param list<string> $erlaubt die für den Pfad erlaubten Methoden
     */
    public function __construct(
        private readonly array $erlaubt,
    ) {
        parent::__construct('Diese Methode ist für den Pfad nicht erlaubt.');
    }

    public function status(): int
    {
        return 405;
    }

    public function errorCode(): string
    {
        return 'method_not_allowed';
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return ['Allow' => implode(', ', $this->erlaubt)];
    }
}
