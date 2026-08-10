<?php

declare(strict_types=1);

namespace App;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

/**
 * Wird geworfen, wenn der Container einen Eintrag zwar kennt, ihn aber
 * nicht bauen kann - etwa bei einer zyklischen Abhängigkeit oder einem
 * Konstruktor-Parameter, der sich nicht auflösen lässt. Erfüllt den
 * allgemeinen PSR-11-Fehlervertrag ContainerExceptionInterface.
 */
final class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
