<?php

declare(strict_types=1);

/**
 * Ein Produkt im Warenkorb.
 */
final class Produkt
{
    public function __construct(
        public readonly string $name,
        public readonly int $preisCent,
    ) {}
}

/**
 * Ein einfacher Warenkorb, dessen Sammlungen PHPDoc genauer beschreibt,
 * als es die nativen Typen können.
 */
final class Warenkorb
{
    /**
     * Die enthaltenen Produkte in Einfügereihenfolge.
     *
     * @var list<Produkt>
     */
    private array $produkte = [];

    public function lege(Produkt $produkt): void
    {
        $this->produkte[] = $produkt;
    }

    /**
     * Zählt, wie oft jeder Produktname vorkommt.
     *
     * @return array<string, int> Name des Produkts zu seiner Anzahl.
     */
    public function haeufigkeiten(): array
    {
        $zaehler = [];
        foreach ($this->produkte as $produkt) {
            $zaehler[$produkt->name] = ($zaehler[$produkt->name] ?? 0) + 1;
        }

        return $zaehler;
    }

    /**
     * Liefert alle Produkte teurer als die Schwelle - faul, per Generator.
     *
     * @param int $schwelleCent Untere Grenze in Cent (ausschließlich).
     *
     * @return iterable<Produkt>
     */
    public function teurerAls(int $schwelleCent): iterable
    {
        foreach ($this->produkte as $produkt) {
            if ($produkt->preisCent > $schwelleCent) {
                yield $produkt;
            }
        }
    }
}

$korb = new Warenkorb();
$korb->lege(new Produkt('Kaffee', 499));
$korb->lege(new Produkt('Kaffee', 499));
$korb->lege(new Produkt('Tee', 299));

foreach ($korb->haeufigkeiten() as $name => $anzahl) {
    echo "$name: $anzahl" . PHP_EOL;
}

foreach ($korb->teurerAls(300) as $produkt) {
    echo 'teuer: ' . $produkt->name . PHP_EOL;
}
