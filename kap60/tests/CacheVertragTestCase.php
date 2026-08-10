<?php

declare(strict_types=1);

namespace App\Cache\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException as PsrInvalidArgumentException;

/**
 * Der gemeinsame Vertragstest für jede PSR-16-Umsetzung.
 *
 * Die Zusagen des Standards werden hier genau einmal formuliert. Jede
 * Unterklasse liefert über die Fabrikmethode erzeugeCache() ihre konkrete
 * Umsetzung; dadurch laufen alle elf Vertragsprüfungen gegen jede Umsetzung.
 */
abstract class CacheVertragTestCase extends TestCase
{
    /** Jede Unterklasse liefert hier die zu prüfende Umsetzung. */
    abstract protected function erzeugeCache(): CacheInterface;

    #[Test]
    public function speichert_einen_wert_und_liest_ihn_wieder(): void
    {
        $cache = $this->erzeugeCache();

        self::assertTrue($cache->set('gruss', 'Hallo Welt'));
        self::assertSame('Hallo Welt', $cache->get('gruss')); // Treffer
    }

    #[Test]
    public function liefert_den_standardwert_bei_einem_fehltreffer(): void
    {
        $cache = $this->erzeugeCache();

        self::assertNull($cache->get('gibt.es.nicht'));
        self::assertSame('ersatz', $cache->get('gibt.es.nicht', 'ersatz'));
    }

    #[Test]
    public function has_meldet_vorhandensein_und_fehlen(): void
    {
        $cache = $this->erzeugeCache();

        self::assertFalse($cache->has('konto'));
        $cache->set('konto', 42);
        self::assertTrue($cache->has('konto'));
    }

    #[Test]
    public function set_ueberschreibt_einen_bestehenden_wert(): void
    {
        $cache = $this->erzeugeCache();

        $cache->set('a', 1);
        $cache->set('a', 2); // derselbe Schlüssel, neuer Wert

        self::assertSame(2, $cache->get('a'));
    }

    #[Test]
    public function delete_entfernt_einen_eintrag(): void
    {
        $cache = $this->erzeugeCache();
        $cache->set('weg', 'da');

        self::assertTrue($cache->delete('weg'));
        self::assertFalse($cache->has('weg'));
    }

    #[Test]
    public function clear_leert_den_ganzen_cache(): void
    {
        $cache = $this->erzeugeCache();
        $cache->set('a', 1);
        $cache->set('b', 2);

        self::assertTrue($cache->clear());
        self::assertFalse($cache->has('a'));
        self::assertFalse($cache->has('b'));
    }

    #[Test]
    public function bewahrt_verschachtelte_werte_unveraendert(): void
    {
        $cache = $this->erzeugeCache();

        // Nicht nur Zeichenketten: jeder serialisierbare Wert ist erlaubt.
        $cache->set('liste', ['a' => 1, 'b' => [2, 3]]);
        self::assertSame(['a' => 1, 'b' => [2, 3]], $cache->get('liste'));
    }

    #[Test]
    public function eine_ttl_in_der_vergangenheit_gilt_sofort_als_abgelaufen(): void
    {
        $cache = $this->erzeugeCache();

        // Eine negative Lebensdauer bedeutet: schon abgelaufen. Der Eintrag
        // darf nie als Treffer erscheinen - ganz ohne echtes Warten.
        $cache->set('kurzlebig', 'wert', ttl: -1);

        self::assertFalse($cache->has('kurzlebig'));
        self::assertSame('weg', $cache->get('kurzlebig', 'weg'));
    }

    #[Test]
    public function ein_leerer_schluessel_wirft_die_psr_ausnahme(): void
    {
        $cache = $this->erzeugeCache();

        $this->expectException(PsrInvalidArgumentException::class);
        $cache->get('');
    }

    #[Test]
    public function ein_reserviertes_zeichen_wirft_die_psr_ausnahme(): void
    {
        $cache = $this->erzeugeCache();

        // Der Doppelpunkt gehört zu den acht reservierten Zeichen. Wir prüfen
        // gegen das Standard-Interface, nicht gegen unsere konkrete Klasse.
        $this->expectException(PsrInvalidArgumentException::class);
        $cache->set('kunde:42', 'wert');
    }

    #[Test]
    public function verarbeitet_mehrere_schluessel_auf_einmal(): void
    {
        $cache = $this->erzeugeCache();

        self::assertTrue($cache->setMultiple(['a' => 1, 'b' => 2]));
        self::assertSame(['a' => 1, 'b' => 2], $cache->getMultiple(['a', 'b']));

        self::assertTrue($cache->deleteMultiple(['a', 'b']));
        self::assertFalse($cache->has('a'));
    }
}
