<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben (Nachher)
 *
 * Die Produktions-Quelle für den Code. Sie kapselt den echten Zufall
 * hinter dem CodeQuelle-Vertrag. random_bytes() liefert kryptografisch
 * sichere Bytes, bin2hex() macht daraus eine lesbare Zeichenkette.
 */
final class ZufallsCodeQuelle implements CodeQuelle
{
    public function naechster(): string
    {
        return strtoupper(bin2hex(random_bytes(4)));
    }
}
