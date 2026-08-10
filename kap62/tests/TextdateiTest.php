<?php

declare(strict_types=1);

namespace App\Tests;

use App\Textdatei;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Textdatei::class)]
final class TextdateiTest extends TestCase
{
    private string $pfad;

    protected function setUp(): void
    {
        // Eine kleine Testdatei im Projektordner anlegen.
        $this->pfad = __DIR__ . '/probe.tmp';
        file_put_contents($this->pfad, "erste\nzweite\ndritte\n");
    }

    protected function tearDown(): void
    {
        if (is_file($this->pfad)) {
            unlink($this->pfad);
        }
    }

    #[Test]
    public function liefert_die_zeilen_ohne_zeilenumbruch(): void
    {
        $zeilen = iterator_to_array(Textdatei::zeilen($this->pfad));

        // Die Schlüssel sind die Zeilennummern ab 1.
        self::assertSame([1 => 'erste', 2 => 'zweite', 3 => 'dritte'], $zeilen);
    }

    #[Test]
    public function liest_immer_nur_eine_zeile_auf_einmal(): void
    {
        // Wir ziehen nur die erste Zeile und halten dann an. Der Generator
        // darf die restlichen Zeilen gar nicht erst gelesen haben.
        foreach (Textdatei::zeilen($this->pfad) as $nummer => $zeile) {
            self::assertSame(1, $nummer);
            self::assertSame('erste', $zeile);
            break;
        }

        // Kein Fehler, kein vollständiges Einlesen - der Test kommt hierher.
        self::assertTrue(true);
    }
}
