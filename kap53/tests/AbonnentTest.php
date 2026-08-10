<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Abonnent;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten
 *
 * Die Domäne lässt sich völlig für sich prüfen - ohne HTTP, ohne
 * Datenbank, ohne jedes Double. Ein Abonnent ist eben nur ein getipptes
 * Datenobjekt.
 */
#[CoversClass(Abonnent::class)]
final class AbonnentTest extends TestCase
{
    #[Test]
    public function ein_neuer_abonnent_hat_noch_keine_id(): void
    {
        $abonnent = Abonnent::neu(
            'ada@example.org',
            new DateTimeImmutable('2026-08-10 09:00:00'),
        );

        self::assertNull($abonnent->id);
        self::assertSame('ada@example.org', $abonnent->email);
    }
}
