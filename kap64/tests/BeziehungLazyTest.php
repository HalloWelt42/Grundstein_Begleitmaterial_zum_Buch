<?php

declare(strict_types=1);

namespace App\Tests;

use App\Bestellhistorie;
use App\KundenRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BeziehungLazyTest extends TestCase
{
    protected function setUp(): void
    {
        Bestellhistorie::$ladungen = 0;
    }

    #[Test]
    public function der_name_laedt_die_historie_nicht(): void
    {
        $kunde = (new KundenRepository())->finde(7, 'Ada Lovelace');

        self::assertSame('Ada Lovelace', $kunde->name);
        self::assertSame(0, Bestellhistorie::$ladungen);
    }

    #[Test]
    public function erst_der_zugriff_laedt_die_historie(): void
    {
        $kunde = (new KundenRepository())->finde(7, 'Ada Lovelace');

        self::assertSame(2, $kunde->historie()->anzahl());
        self::assertSame(1, Bestellhistorie::$ladungen);
    }
}
