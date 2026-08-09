<?php

declare(strict_types=1);

namespace App\Cart;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Ein einfacher Warenkorb: Jede Position ist ein Name plus ein Money-Betrag.
 * Die eindeutige Kennung stammt aus dem Fremdpaket ramsey/uuid.
 */
final class Cart
{
    private readonly UuidInterface $id;

    /** @var list<array{name: string, preis: Money}> */
    private array $positionen = [];

    public function __construct()
    {
        // Uuid stammt aus vendor/ - der Autoloader findet die Klasse selbst.
        $this->id = Uuid::uuid4();
    }

    public function id(): string
    {
        return $this->id->toString();
    }

    public function add(string $name, Money $preis): void
    {
        $this->positionen[] = ['name' => $name, 'preis' => $preis];
    }

    public function summe(): Money
    {
        $summe = new Money(0);
        foreach ($this->positionen as $position) {
            $summe = $summe->plus($position['preis']);
        }

        return $summe;
    }

    public function beleg(): string
    {
        $zeilen = [];
        foreach ($this->positionen as $position) {
            $zeilen[] = sprintf('- %s: %s', $position['name'], $position['preis']->format());
        }
        $zeilen[] = 'Summe: ' . $this->summe()->format();

        return implode("\n", $zeilen);
    }
}
