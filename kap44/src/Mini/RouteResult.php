<?php

declare(strict_types=1);

namespace Grundstein\Mini;

/**
 * Das Ergebnis von Router::match(). Es bündelt den Status mit allem, was
 * der Dispatcher danach braucht: bei einem Treffer den Handler und die
 * ausgelesenen Pfadparameter, bei einer nicht erlaubten Methode die Liste
 * der erlaubten Methoden für den Allow-Header.
 */
final class RouteResult
{
    /**
     * @param callable|null         $handler        gesetzt nur bei Found
     * @param array<string, string> $params         ausgelesene Pfadparameter
     * @param list<string>          $allowedMethods gesetzt nur bei MethodNotAllowed
     */
    public function __construct(
        public readonly RouteStatus $status,
        public readonly mixed $handler = null,
        public readonly array $params = [],
        public readonly array $allowedMethods = [],
    ) {
    }
}
