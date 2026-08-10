<?php

declare(strict_types=1);

namespace Grundstein\Mini;

/**
 * Eine einzelne registrierte Route. Sie hält die Methode, das aus dem
 * Muster übersetzte reguläre Ausdruck, die Namen der Pfadparameter und
 * den Handler, der bei einem Treffer aufgerufen wird.
 *
 * Das Objekt ist unveränderlich: Eine einmal registrierte Route ändert
 * sich nicht mehr. Der Handler ist ein beliebiges Callable - eine Closure
 * oder ein Objekt mit __invoke(); getippt ist er über den phpdoc-Eintrag.
 */
final class Route
{
    /**
     * @param list<string> $paramNames Namen der {platzhalter} im Muster
     * @param callable      $handler    wird bei einem Treffer aufgerufen
     */
    public function __construct(
        public readonly string $method,
        public readonly string $regex,
        public readonly array $paramNames,
        public readonly mixed $handler,
    ) {
    }
}
