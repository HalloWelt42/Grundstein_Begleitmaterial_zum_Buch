<?php

declare(strict_types=1);

namespace Grundstein\Api;

/**
 * Die angefragte Ressource gibt es nicht - sei es, weil kein Pfad passt,
 * sei es, weil es den Kunden mit dieser id nicht gibt. Antwort: 404 Not
 * Found.
 */
final class NotFoundException extends ApiException
{
    public function status(): int
    {
        return 404;
    }

    public function errorCode(): string
    {
        return 'not_found';
    }
}
