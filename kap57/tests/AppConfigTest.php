<?php

declare(strict_types=1);

namespace App\Tests;

use App\AppConfig;
use App\ConfigFehler;
use App\Env;
use App\Secret;
use App\Umgebung;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Prüft das typisierte Konfigurationsobjekt: den Aufbau aus rohen Werten,
 * seine Invarianten, den Fehlerfall eines fehlenden Pflichtwerts und die
 * Geheimnis-Hygiene (nie im Klartext nach draußen).
 */
final class AppConfigTest extends TestCase
{
    /**
     * @param array<string, string> $extra
     */
    private function envMit(array $extra = []): Env
    {
        return new Env(array_merge([
            'APP_ENV'   => 'dev',
            'APP_DEBUG' => 'true',
            'DB_DSN'    => 'sqlite::memory:',
            'MAIL_FROM' => 'team@example.test',
            'API_KEY'   => 'geheim-123',
        ], $extra));
    }

    #[Test]
    public function baut_ein_gueltiges_config_objekt_aus_rohen_werten(): void
    {
        $config = AppConfig::ausEnv($this->envMit());

        self::assertSame(Umgebung::Entwicklung, $config->umgebung);
        self::assertTrue($config->debug);
        self::assertSame('sqlite::memory:', $config->dbDsn);
        self::assertSame('team@example.test', $config->mailFrom);
        // Der Klartext ist nur über die bewusste Methode erreichbar.
        self::assertSame('geheim-123', $config->apiSchluessel->offenbare());
    }

    #[Test]
    public function maskiert_das_geheimnis_in_der_anzeige(): void
    {
        $config  = AppConfig::ausEnv($this->envMit(['API_KEY' => 'super-geheim']));
        $anzeige = $config->alsAnzeige();

        self::assertSame('***', $anzeige['apiSchluessel']);

        // Auch als JSON serialisiert taucht das Geheimnis nirgends auf.
        $json = (string) json_encode($anzeige);
        self::assertStringNotContainsString('super-geheim', $json);
    }

    #[Test]
    public function ein_secret_zeigt_sich_beim_ausgeben_nur_maskiert(): void
    {
        $secret = new Secret('streng-geheim');

        self::assertSame('***', (string) $secret);
        self::assertStringNotContainsString('streng-geheim', print_r($secret, true));
        self::assertSame('streng-geheim', $secret->offenbare());
    }

    #[Test]
    public function verbietet_offene_fehlerausgabe_in_der_produktion(): void
    {
        $this->expectException(ConfigFehler::class);
        $this->expectExceptionMessage('APP_DEBUG');

        AppConfig::ausEnv($this->envMit(['APP_ENV' => 'prod', 'APP_DEBUG' => 'true']));
    }

    #[Test]
    public function in_der_produktion_mit_ausgeschaltetem_debug_ist_alles_gut(): void
    {
        $config = AppConfig::ausEnv($this->envMit(['APP_ENV' => 'prod', 'APP_DEBUG' => 'false']));

        self::assertSame(Umgebung::Produktion, $config->umgebung);
        self::assertFalse($config->debug);
    }

    #[Test]
    public function wirft_bei_fehlendem_pflichtwert_und_nennt_nur_den_schluessel(): void
    {
        // Kein API_KEY - das Geheimnis fehlt.
        $env = new Env([
            'APP_ENV'   => 'dev',
            'DB_DSN'    => 'sqlite::memory:',
            'MAIL_FROM' => 'team@example.test',
        ]);

        try {
            AppConfig::ausEnv($env);
            self::fail('Erwartet wurde eine ConfigFehler-Ausnahme.');
        } catch (ConfigFehler $fehler) {
            // Die Meldung nennt den Schlüssel, aber keinen Wert.
            self::assertStringContainsString('API_KEY', $fehler->getMessage());
        }
    }

    #[Test]
    public function weist_eine_unbekannte_umgebung_ab(): void
    {
        $this->expectException(ConfigFehler::class);
        $this->expectExceptionMessage('APP_ENV');

        AppConfig::ausEnv($this->envMit(['APP_ENV' => 'abnahme']));
    }

    #[Test]
    public function weist_eine_ungueltige_absenderadresse_ab(): void
    {
        $this->expectException(ConfigFehler::class);
        $this->expectExceptionMessage('MAIL_FROM');

        AppConfig::ausEnv($this->envMit(['MAIL_FROM' => 'kein-at-zeichen']));
    }
}
