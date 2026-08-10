<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\EmailAdresse;
use App\Domain\Kunde;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class KundeTest extends TestCase
{
    #[Test]
    public function ein_kunde_bleibt_derselbe_trotz_geaenderter_werte(): void
    {
        $kunde = new Kunde(7, 'Ada', new EmailAdresse('ada@example.org'));

        $kunde->benenneUm('Ada Lovelace');
        $kunde->aendereEmail(new EmailAdresse('ada@rechenwerk.example'));

        // Die Identität (id) bleibt, die Werte haben sich geändert.
        self::assertSame(7, $kunde->id());
        self::assertSame('Ada Lovelace', $kunde->name());
        self::assertSame('ada@rechenwerk.example', $kunde->email()->wert);
    }

    #[Test]
    public function gleiche_id_bedeutet_derselbe_kunde(): void
    {
        $einer = new Kunde(7, 'Ada', new EmailAdresse('ada@example.org'));
        $anderer = new Kunde(7, 'Grace', new EmailAdresse('grace@example.org'));

        // Verschiedene Werte, gleiche Identität - also gleich.
        self::assertTrue($einer->istGleich($anderer));
    }

    #[Test]
    public function verschiedene_id_bedeutet_verschiedene_kunden(): void
    {
        $einer = new Kunde(7, 'Ada', new EmailAdresse('ada@example.org'));
        $anderer = new Kunde(8, 'Ada', new EmailAdresse('ada@example.org'));

        // Gleiche Werte, aber verschiedene Identität - also verschieden.
        self::assertFalse($einer->istGleich($anderer));
    }
}
