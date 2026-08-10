<?php

declare(strict_types=1);

namespace App\Tests;

use App\HealthCheck;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Die Gesundheitsprüfung ist testbar, weil ihre Einzelprüfungen als
 * Closures hereingereicht werden. Im Test setzen wir sie fest auf gesund
 * oder krank - ganz ohne echten Cache, echtes Dateisystem oder Zufall.
 */
final class HealthCheckTest extends TestCase
{
    #[Test]
    public function status_ist_ok_wenn_alle_pruefungen_bestehen(): void
    {
        $check = new HealthCheck('1.0.0', [
            'a' => static fn (): bool => true,
            'b' => static fn (): bool => true,
        ]);

        $ergebnis = $check->ausfuehren();

        self::assertSame('ok', $ergebnis['status']);
        self::assertSame('1.0.0', $ergebnis['version']);
        self::assertSame(['a' => 'ok', 'b' => 'ok'], $ergebnis['checks']);
    }

    #[Test]
    public function eine_einzige_kranke_pruefung_kippt_den_gesamtstatus(): void
    {
        $check = new HealthCheck('1.0.0', [
            'a' => static fn (): bool => true,
            'b' => static fn (): bool => false,
        ]);

        $ergebnis = $check->ausfuehren();

        self::assertSame('fehler', $ergebnis['status']);
        self::assertSame(['a' => 'ok', 'b' => 'fehler'], $ergebnis['checks']);
    }
}
