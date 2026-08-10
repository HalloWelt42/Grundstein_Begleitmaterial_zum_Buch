<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Ein schmaler Vertrag für die aktuelle Zeit. Statt im Service direkt
 * new DateTimeImmutable() aufzurufen, reichen wir diese Uhr hinein. So
 * lässt sich die Zeit im Test auf einen festen Wert setzen - sonst wäre
 * jeder Testlauf von der echten Systemuhr abhängig und nicht mehr
 * wiederholbar.
 */
interface Clock
{
    public function jetzt(): DateTimeImmutable;
}
