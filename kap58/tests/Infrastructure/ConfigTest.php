<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure;

use App\Infrastructure\Config\Config;
use App\Infrastructure\Config\Umgebung;
use App\Infrastructure\Config\UmgebungsdateiLeser;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Unit-Tests für die Konfiguration (Kapitel 57): das typisierte Config-Objekt
 * aus rohen Werten und der Leser der Umgebungsdatei. Beides sind reine
 * Funktionen ohne Nebenwirkung - abgesehen vom Lesen einer Testdatei, die der
 * Test selbst anlegt und wieder entfernt.
 */
final class ConfigTest extends TestCase
{
    #[Test]
    public function baut_ein_typisiertes_objekt_aus_rohen_werten(): void
    {
        $config = Config::ausWerten([
            'APP_NAME'     => 'Bestellannahme',
            'APP_UMGEBUNG' => 'produktion',
            'DB_DSN'       => 'sqlite::memory:',
        ]);

        self::assertSame('Bestellannahme', $config->appName);
        self::assertSame(Umgebung::Produktion, $config->umgebung);
        self::assertFalse($config->istEntwicklung());
    }

    #[Test]
    public function fehlende_werte_bekommen_sinnvolle_standardwerte(): void
    {
        $config = Config::ausWerten([]);

        self::assertSame('Grundstein', $config->appName);
        self::assertSame(Umgebung::Entwicklung, $config->umgebung);
        self::assertTrue($config->istEntwicklung());
    }

    #[Test]
    public function eine_unbekannte_umgebung_ist_ein_harter_fehler(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Config::ausWerten(['APP_UMGEBUNG' => 'produktiv']);
    }

    #[Test]
    public function der_leser_ueberspringt_kommentare_und_leerzeilen(): void
    {
        $pfad = tempnam(sys_get_temp_dir(), 'env');
        file_put_contents($pfad, <<<'ENV'
            # eine Kommentarzeile
            APP_NAME="Bestellannahme"

            APP_UMGEBUNG=produktion
            ENV);

        $werte = (new UmgebungsdateiLeser())->lies($pfad);
        unlink($pfad);

        // Anführungszeichen entfernt, Kommentar und Leerzeile übersprungen.
        self::assertSame('Bestellannahme', $werte['APP_NAME']);
        self::assertSame('produktion', $werte['APP_UMGEBUNG']);
        self::assertArrayNotHasKey('# eine Kommentarzeile', $werte);
    }

    #[Test]
    public function der_leser_liefert_leer_wenn_die_datei_fehlt(): void
    {
        self::assertSame([], (new UmgebungsdateiLeser())->lies('/gibt/es/nicht.env'));
    }
}
