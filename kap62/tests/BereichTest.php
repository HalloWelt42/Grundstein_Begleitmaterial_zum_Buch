<?php

declare(strict_types=1);

namespace App\Tests;

use App\Bereich;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bereich::class)]
final class BereichTest extends TestCase
{
    #[Test]
    public function laeuft_in_schritten_ueber_den_bereich(): void
    {
        $werte = iterator_to_array(new Bereich(0, 10, 2), false);

        self::assertSame([0, 2, 4, 6, 8, 10], $werte);
    }

    #[Test]
    public function laesst_sich_mehrfach_durchlaufen(): void
    {
        // Anders als ein Generator ist ein handgeschriebener Iterator
        // rückspulbar: Zwei foreach-Durchläufe liefern dasselbe Ergebnis.
        $bereich = new Bereich(1, 3);

        $ersterLauf  = iterator_to_array($bereich, false);
        $zweiterLauf = iterator_to_array($bereich, false);

        self::assertSame([1, 2, 3], $ersterLauf);
        self::assertSame($ersterLauf, $zweiterLauf);
    }
}
