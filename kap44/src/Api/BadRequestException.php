<?php

declare(strict_types=1);

namespace Grundstein\Api;

/**
 * Die Anfrage selbst ist fehlerhaft, noch bevor es um einzelne Feldwerte
 * geht: ein leerer Rumpf, wo einer erwartet wird, oder ein Rumpf, der kein
 * gültiges JSON ist. Antwort: 400 Bad Request.
 */
final class BadRequestException extends ApiException
{
    public function status(): int
    {
        return 400;
    }

    public function errorCode(): string
    {
        return 'bad_request';
    }
}
