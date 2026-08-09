<?php

declare(strict_types=1);

/**
 * Wirft, wenn ein Rabatt außerhalb des erlaubten Bereichs liegt.
 */
final class UngueltigerRabatt extends \InvalidArgumentException
{
}

/**
 * Zieht einen prozentualen Rabatt von einem Centbetrag ab.
 *
 * Gerundet wird kaufmännisch auf ganze Cent. Ein Rabatt von 0 bis 100
 * ist erlaubt; alles darüber oder darunter ist ein Programmierfehler
 * und keine gültige Eingabe.
 *
 * @param int   $cent    Ausgangsbetrag in Cent, niemals negativ.
 * @param float $prozent Rabatt in Prozent, im Bereich 0 bis 100.
 *
 * @return int Der rabattierte Betrag in Cent.
 *
 * @throws UngueltigerRabatt wenn $prozent nicht zwischen 0 und 100 liegt.
 */
function wendeRabattAn(int $cent, float $prozent): int
{
    if ($prozent < 0.0 || $prozent > 100.0) {
        throw new UngueltigerRabatt("Rabatt $prozent liegt nicht zwischen 0 und 100.");
    }

    return (int) round($cent * (1 - $prozent / 100));
}

echo wendeRabattAn(1299, 10.0) . PHP_EOL;

try {
    wendeRabattAn(1299, 150.0);
} catch (UngueltigerRabatt $e) {
    echo 'Abgefangen: ' . $e->getMessage() . PHP_EOL;
}
