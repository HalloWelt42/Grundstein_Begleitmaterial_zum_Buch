<?php

declare(strict_types=1);

namespace App\Cache\Tests;

use App\Cache\DateiCache;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\SimpleCache\CacheInterface;

#[CoversClass(DateiCache::class)]
final class DateiCacheTest extends CacheVertragTestCase
{
    private string $verzeichnis;

    protected function setUp(): void
    {
        // Für jeden Test ein eigenes, frisches Verzeichnis - so beeinflussen
        // sich die Tests nicht gegenseitig (dieselbe Isolation wie bei den
        // Integrationstests aus Kapitel 50).
        $this->verzeichnis = sys_get_temp_dir() . '/kap60-cache-' . bin2hex(random_bytes(6));
    }

    protected function tearDown(): void
    {
        // Das Verzeichnis nach dem Test wieder abräumen.
        foreach (glob($this->verzeichnis . '/*') ?: [] as $datei) {
            @unlink($datei);
        }

        @rmdir($this->verzeichnis);
    }

    protected function erzeugeCache(): CacheInterface
    {
        return new DateiCache($this->verzeichnis);
    }

    #[Test]
    public function ein_wert_ueberdauert_eine_neue_instanz(): void
    {
        $ersteInstanz = new DateiCache($this->verzeichnis);
        $ersteInstanz->set('bleibt', 'erhalten');

        // Eine ganz neue Instanz auf demselben Verzeichnis findet den Wert -
        // genau der Unterschied zum flüchtigen ArrayCache.
        $zweiteInstanz = new DateiCache($this->verzeichnis);
        self::assertSame('erhalten', $zweiteInstanz->get('bleibt'));
    }

    #[Test]
    public function legt_das_verzeichnis_bei_bedarf_an(): void
    {
        $tief = $this->verzeichnis . '/tief/verschachtelt';

        new DateiCache($tief);

        self::assertDirectoryExists($tief);
    }

    #[Test]
    public function eine_beschaedigte_datei_gilt_als_fehltreffer(): void
    {
        $cache = new DateiCache($this->verzeichnis);
        $cache->set('kaputt', 'wert');

        // Die Cache-Datei mit Müll überschreiben, den unserialize() nicht liest.
        $datei = glob($this->verzeichnis . '/*.cache')[0];
        file_put_contents($datei, 'kein gültiger serialize-String');

        self::assertSame('fehltreffer', $cache->get('kaputt', 'fehltreffer'));
    }
}
