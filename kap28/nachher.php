<?php

declare(strict_types=1);

// NACHHER: dieselbe Aufgabe, in kleine Bausteine mit genau einer
// Verantwortung zerlegt. Sprechende Namen, frühe Rückgabe statt tiefer
// Verschachtelung, und ein Rabatt-Vertrag, hinter dem beliebige Rabatte
// austauschbar sind. Die Ausgabe ist identisch mit der aus vorher.php.

/**
 * Der Vertrag für jede Art von Rabatt. Wer ihn erfüllt, liefert den
 * Abzug in Cent für eine gegebene Zwischensumme - mehr nicht.
 */
interface Rabatt
{
    public function abzug(int $zwischensumme): int;
}

/**
 * Kein Rabatt: der Abzug ist immer null.
 */
final class KeinRabatt implements Rabatt
{
    public function abzug(int $zwischensumme): int
    {
        return 0;
    }
}

/**
 * Ein prozentualer Rabatt auf die Zwischensumme.
 */
final class ProzentRabatt implements Rabatt
{
    public function __construct(
        private readonly int $prozent,
    ) {}

    public function abzug(int $zwischensumme): int
    {
        // Ganzzahlig rechnen, damit keine Cent-Bruchteile entstehen.
        return intdiv($zwischensumme * $this->prozent, 100);
    }
}

/**
 * Eine einzelne Position im Warenkorb: Name, Einzelpreis in Cent, Menge.
 */
final class Position
{
    public function __construct(
        public readonly string $name,
        public readonly int $einzelpreis,
        public readonly int $menge,
    ) {}

    public function gesamtpreis(): int
    {
        return $this->einzelpreis * $this->menge;
    }
}

/**
 * Der Warenkorb kennt seine Posten und seinen Rabatt und beantwortet
 * genau die Fragen, die zu ihm gehören.
 */
final class Warenkorb
{
    /**
     * @param list<Position> $posten
     */
    public function __construct(
        private readonly array $posten,
        private readonly Rabatt $rabatt,
    ) {}

    public function istLeer(): bool
    {
        return $this->posten === [];
    }

    public function zwischensumme(): int
    {
        $summe = 0;
        foreach ($this->posten as $posten) {
            $summe += $posten->gesamtpreis();
        }

        return $summe;
    }

    public function endbetrag(): int
    {
        $zwischensumme = $this->zwischensumme();

        return $zwischensumme - $this->rabatt->abzug($zwischensumme);
    }
}

/**
 * Formatiert einen Cent-Betrag als deutschen Euro-Text.
 */
function euroFormat(int $cent): string
{
    return number_format($cent / 100, 2, ',', '.');
}

/**
 * Baut die Belegzeile für einen Warenkorb. Der Sonderfall steht vorn
 * und kehrt früh zurück - der Normalfall bleibt unverschachtelt.
 */
function belegzeile(Warenkorb $korb): string
{
    if ($korb->istLeer()) {
        return 'Keine Posten';
    }

    return 'Summe: ' . euroFormat($korb->endbetrag()) . ' EUR';
}

$gold = new Warenkorb(
    [
        new Position('Tastatur', 5000, 2),
        new Position('Maus', 2500, 1),
    ],
    new ProzentRabatt(20),
);

$standard = new Warenkorb(
    [new Position('Kabel', 999, 3)],
    new KeinRabatt(),
);

$leer = new Warenkorb([], new KeinRabatt());

echo belegzeile($gold) . PHP_EOL;
echo belegzeile($standard) . PHP_EOL;
echo belegzeile($leer) . PHP_EOL;
