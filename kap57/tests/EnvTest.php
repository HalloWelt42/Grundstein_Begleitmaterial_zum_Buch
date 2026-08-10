<?php

declare(strict_types=1);

namespace App\Tests;

use App\ConfigFehler;
use App\Env;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Prüft den .env-Leser: das robuste Parsen einer Datei, die Vorrangfolge der
 * drei Quellen und die Zusicherungen der Zugriffsmethoden.
 */
final class EnvTest extends TestCase
{
    private string $pfad;

    protected function setUp(): void
    {
        // Eine echte kleine .env-Datei mit allen Stolpersteinen anlegen.
        $this->pfad = (string) tempnam(sys_get_temp_dir(), 'env');
        file_put_contents($this->pfad, <<<'ENV'
            # Ein Kommentar am Zeilenanfang
            APP_ENV=test

              # eingerückter Kommentar und darüber eine Leerzeile
            DB_DSN="sqlite::memory:"
            MAIL_FROM='team@example.test'
            LEER=
            MIT_GLEICH=a=b=c
            export EXPORTIERT=ja
            OHNE_GLEICH_ZEICHEN
            ENV);
    }

    protected function tearDown(): void
    {
        @unlink($this->pfad);
    }

    #[Test]
    public function parst_kommentare_leerzeilen_und_anfuehrungszeichen(): void
    {
        $werte = Env::parseDatei($this->pfad);

        self::assertSame([
            'APP_ENV'    => 'test',
            'DB_DSN'     => 'sqlite::memory:',   // Anführungszeichen abgestreift
            'MAIL_FROM'  => 'team@example.test', // einfache Anführungszeichen auch
            'LEER'       => '',                  // leerer Wert bleibt leer
            'MIT_GLEICH' => 'a=b=c',             // nur am ersten '=' getrennt
            'EXPORTIERT' => 'ja',                // führendes 'export ' entfernt
        ], $werte);
    }

    #[Test]
    public function eine_fehlende_datei_ist_kein_fehler(): void
    {
        self::assertSame([], Env::parseDatei('/gibt/es/nicht/.env'));
    }

    #[Test]
    public function echte_umgebung_schlaegt_datei_schlaegt_standardwerte(): void
    {
        $env = Env::aus(
            // Standardwerte (schwach): NUR_STANDARD kommt in keiner anderen Quelle vor
            ['APP_ENV' => 'dev', 'DB_DSN' => 'aus-standard', 'NUR_STANDARD' => 'bleibt'],
            // .env-Datei (mittel): setzt DB_DSN neu, lässt APP_ENV stehen
            $this->pfad,
            // echte Umgebung (stark): überschreibt APP_ENV
            ['APP_ENV' => 'prod'],
        );

        self::assertSame('prod', $env->hole('APP_ENV'));            // echte Umgebung gewinnt
        self::assertSame('sqlite::memory:', $env->hole('DB_DSN'));  // .env schlägt Standard
        self::assertSame('bleibt', $env->hole('NUR_STANDARD'));     // Standard bleibt, wo nichts überschreibt
    }

    #[Test]
    public function hole_liefert_den_standardwert_wenn_der_schluessel_fehlt(): void
    {
        $env = new Env(['A' => '1']);

        self::assertSame('1', $env->hole('A'));
        self::assertNull($env->hole('B'));
        self::assertSame('vorgabe', $env->hole('B', 'vorgabe'));
    }

    #[Test]
    public function pflicht_wirft_bei_fehlendem_wert(): void
    {
        $env = new Env([]);

        $this->expectException(ConfigFehler::class);
        $this->expectExceptionMessage('API_KEY');

        $env->pflicht('API_KEY');
    }

    #[Test]
    public function bool_deutet_uebliche_wahrheitswerte(): void
    {
        $env = new Env([
            'A' => 'true', 'B' => '1', 'C' => 'on', 'D' => 'yes',
            'E' => 'false', 'F' => '0', 'G' => 'nein',
        ]);

        self::assertTrue($env->bool('A'));
        self::assertTrue($env->bool('B'));
        self::assertTrue($env->bool('C'));
        self::assertTrue($env->bool('D'));

        self::assertFalse($env->bool('E'));
        self::assertFalse($env->bool('F'));
        self::assertFalse($env->bool('G'));

        // Fehlender Schlüssel: der übergebene Standardwert greift.
        self::assertTrue($env->bool('X', true));
        self::assertFalse($env->bool('X', false));
    }
}
