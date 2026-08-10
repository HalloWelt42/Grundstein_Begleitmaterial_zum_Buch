<?php

declare(strict_types=1);

namespace Grundstein\Api;

/**
 * Die Anfrage war wohlgeformt und der Rumpf war gültiges JSON - aber die
 * darin enthaltenen Werte sind fachlich unzulässig: ein leerer Name, eine
 * kaputte E-Mail-Adresse. Antwort: 422 Unprocessable Entity. Die einzelnen
 * Feldfehler trägt die Ausnahme in details() mit, damit der Client genau
 * weiß, was zu korrigieren ist.
 */
final class ValidationException extends ApiException
{
    /**
     * @param array<string, string> $fehler Feldname auf Fehlermeldung
     */
    public function __construct(
        string $nachricht,
        private readonly array $fehler,
    ) {
        parent::__construct($nachricht);
    }

    public function status(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'validation_failed';
    }

    /**
     * @return array<string, string>
     */
    public function details(): array
    {
        return $this->fehler;
    }
}
