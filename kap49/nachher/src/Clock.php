<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben (Nachher)
 *
 * Ein schmaler Vertrag für die aktuelle Zeit - genau wie in Kapitel 48.
 * Statt new DateTimeImmutable() mitten in der Logik aufzurufen, reichen
 * wir eine Uhr über den Konstruktor herein. Das ist die Naht, an der im
 * Test eine feste Uhr eintritt.
 */
interface Clock
{
    public function jetzt(): DateTimeImmutable;
}
