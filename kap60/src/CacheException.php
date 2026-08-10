<?php

declare(strict_types=1);

namespace App\Cache;

use Psr\SimpleCache\CacheException as PsrCacheException;

/**
 * Allgemeiner Fehler beim Betrieb des Caches - etwa wenn sich das
 * Cache-Verzeichnis nicht anlegen lässt.
 *
 * Wie die InvalidArgumentException erfüllt sie den passenden
 * PSR-16-Vertrag, damit der Aufrufer sie standardkonform behandeln kann.
 */
final class CacheException extends \RuntimeException implements PsrCacheException
{
}
