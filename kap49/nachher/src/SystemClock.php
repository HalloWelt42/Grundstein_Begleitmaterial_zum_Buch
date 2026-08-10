<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben (Nachher)
 *
 * Die Produktions-Uhr. Sie ist die einzige erlaubte Stelle, an der
 * new DateTimeImmutable() für die aktuelle Zeit steht. Weil sie hinter
 * dem Clock-Vertrag liegt, kann der Test sie durch eine feste Uhr
 * ersetzen, ohne dass der Rest der Anwendung etwas davon merkt. Dieselbe
 * Idee normiert später PSR-20 (Teil IX).
 */
final class SystemClock implements Clock
{
    public function jetzt(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
