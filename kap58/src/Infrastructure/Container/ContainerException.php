<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Der allgemeine PSR-11-Oberbegriff für jeden Fehler beim Bauen eines Dienstes
 * (Kapitel 52). In diesem schlanken Container ohne Autowiring wird sie selten
 * gebraucht, gehört aber zum vollständigen Vertrag dazu.
 */
final class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
