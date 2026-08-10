<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben (Nachher)
 *
 * Der Vertrag für den Zufall. Statt random_bytes() mitten in der Logik
 * aufzurufen, holt sich der Dienst den nächsten Code über diesen Vertrag.
 * So kann der Test einen festen, bekannten Code einsetzen - Zufall und
 * wiederholbarer Test vertragen sich sonst nicht.
 */
interface CodeQuelle
{
    public function naechster(): string;
}
