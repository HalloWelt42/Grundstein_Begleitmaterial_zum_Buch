<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Die PSR-11-Ausnahme für einen unbekannten Bezeichner. Sie erfüllt das
 * Standard-Interface, damit jeder Aufrufer, der PSR-11 kennt, den Fehlerfall
 * einheitlich behandeln kann (Kapitel 52).
 */
final class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
