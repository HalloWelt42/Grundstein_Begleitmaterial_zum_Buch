<?php

declare(strict_types=1);

namespace App;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Wird geworfen, wenn der Container nach einem Bezeichner gefragt wird,
 * den er nicht kennt. Sie erfüllt den PSR-11-Vertrag
 * NotFoundExceptionInterface - so weiß jeder Aufrufer, der den Standard
 * kennt, genau, was ein "nicht gefunden" bedeutet.
 */
final class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
