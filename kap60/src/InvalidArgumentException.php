<?php

declare(strict_types=1);

namespace App\Cache;

use Psr\SimpleCache\InvalidArgumentException as PsrInvalidArgumentException;

/**
 * Wird geworfen, wenn ein Cache-Schlüssel nicht den Regeln von PSR-16
 * entspricht - etwa weil er leer ist oder ein reserviertes Zeichen enthält.
 *
 * Sie erfüllt den PSR-16-Vertrag, indem sie das dortige Interface umsetzt.
 * So kann ein Aufrufer, der nur den Standard kennt, sie richtig fangen -
 * gleich, welche konkrete Cache-Klasse dahintersteckt.
 */
final class InvalidArgumentException extends \InvalidArgumentException implements PsrInvalidArgumentException
{
}
