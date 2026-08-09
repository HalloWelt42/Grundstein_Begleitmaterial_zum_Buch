<?php

declare(strict_types=1);

/**
 * Eine einzelne Bestellposition mit Artikel und Menge. Weil es sich um ein
 * Objekt handelt, wird es bei einer flachen Kopie nur per Referenz geteilt.
 */
final class Position
{
    public function __construct(
        public string $artikel,
        public int $menge,
    ) {}
}

/**
 * Ein Warenkorb hält eine Liste von Position-Objekten. Damit eine Kopie
 * wirklich unabhängig ist, klont __clone jede Position einzeln (tiefe Kopie).
 */
final class Warenkorb
{
    /** @var list<Position> */
    private array $positionen = [];

    public function lege(Position $p): void
    {
        $this->positionen[] = $p;
    }

    public function erhoeheErste(): void
    {
        $this->positionen[0]->menge++;
    }

    public function ersteMenge(): int
    {
        return $this->positionen[0]->menge;
    }

    public function __clone(): void
    {
        // Jede Position einzeln klonen, damit die Kopie unabhängig ist.
        $this->positionen = array_map(
            fn (Position $p): Position => clone $p,
            $this->positionen,
        );
    }
}

$korb = new Warenkorb();
$korb->lege(new Position('Buch', 1));

$kopie = clone $korb;
$kopie->erhoeheErste();

echo "Original: {$korb->ersteMenge()}\n";
echo "Kopie:    {$kopie->ersteMenge()}\n";
