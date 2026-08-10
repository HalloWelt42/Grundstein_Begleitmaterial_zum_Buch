<?php

declare(strict_types=1);

namespace App\Tests;

use App\Geldbetrag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 67: Internationalisierung
 *
 * Der Geldbetrag speichert neutral (Cent als int, Währung als ASCII) und
 * lokalisiert nur zur Anzeige. Die Tests prüfen bewusst nur die stabilen
 * Eigenschaften der Ausgabe (Trennzeichen, Stellung des Symbols), nicht die
 * exakten Leerzeichen - deren Codepunkte hängen von der ICU-Version ab.
 */
#[CoversClass(Geldbetrag::class)]
final class GeldbetragTest extends TestCase
{
    #[Test]
    public function speichert_den_zustand_sprachneutral(): void
    {
        $betrag = new Geldbetrag(4990, 'EUR');

        self::assertSame(4990, $betrag->cent);
        self::assertSame('EUR', $betrag->waehrung);
    }

    #[Test]
    public function deutsch_setzt_das_symbol_hinter_den_betrag(): void
    {
        $betrag = new Geldbetrag(4990, 'EUR');

        $text = $betrag->formatiere('de-DE');

        // Deutsch: Komma als Dezimaltrenner, Symbol am Ende.
        self::assertStringContainsString('49,90', $text);
        self::assertStringEndsWith("\u{20ac}", $text); // Euro-Zeichen
    }

    #[Test]
    public function englisch_setzt_das_symbol_vor_den_betrag(): void
    {
        $betrag = new Geldbetrag(4990, 'EUR');

        $text = $betrag->formatiere('en-US');

        // Englisch (US): Punkt als Dezimaltrenner, Symbol am Anfang.
        self::assertStringContainsString('49.90', $text);
        self::assertStringStartsWith("\u{20ac}", $text);
    }

    #[Test]
    public function derselbe_betrag_sieht_je_sprachraum_anders_aus(): void
    {
        $betrag = new Geldbetrag(4990, 'EUR');

        // Ein gespeicherter Wert, zwei verschiedene Darstellungen.
        self::assertNotSame(
            $betrag->formatiere('de-DE'),
            $betrag->formatiere('en-US'),
        );
    }
}
