<?php

declare(strict_types=1);

namespace App\Cache\Tests;

use App\Cache\ArrayCache;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\SimpleCache\CacheInterface;

#[CoversClass(ArrayCache::class)]
final class ArrayCacheTest extends CacheVertragTestCase
{
    protected function erzeugeCache(): CacheInterface
    {
        return new ArrayCache();
    }

    #[Test]
    public function ein_eintrag_laeuft_nach_echter_zeit_ab(): void
    {
        $cache = new ArrayCache();
        $cache->set('kurzlebig', 'da', ttl: 1);

        self::assertTrue($cache->has('kurzlebig')); // sofort noch vorhanden

        sleep(2); // die eine Sekunde Lebensdauer sicher überschreiten

        self::assertFalse($cache->has('kurzlebig')); // jetzt abgelaufen
    }
}
