<?php

declare(strict_types=1);

/**
 * Bewusst abgespeckte Konto-Klasse: Sie trägt nur den Saldo und dient
 * allein dazu, die Referenzsemantik von Objekten zu zeigen.
 */
final class Konto
{
    private int $saldo;

    public function __construct(int $startSaldo = 0)
    {
        $this->saldo = $startSaldo;
    }

    public function einzahlen(int $betrag): void
    {
        $this->saldo += $betrag;
    }

    public function saldo(): int
    {
        return $this->saldo;
    }
}

// Ein Objekt erzeugen und einer zweiten Variablen zuweisen.
$a = new Konto(100);
$b = $a; // Kopiert NICHT das Objekt, sondern nur die Referenz darauf.

// Eine Einzahlung über $b ...
$b->einzahlen(50);

// ... ist auch über $a sichtbar: beide Variablen zeigen auf dasselbe Objekt.
echo 'Saldo über $a: ' . $a->saldo() . "\n";
echo 'Saldo über $b: ' . $b->saldo() . "\n";

// Zum Vergleich ein Skalar: hier wird bei der Zuweisung wirklich kopiert.
$x = 100;
$y = $x;
$y += 50;
echo 'Skalar $x: ' . $x . "\n";
echo 'Skalar $y: ' . $y . "\n";

// Ein echtes zweites Objekt entsteht nur mit new ...
$c = new Konto(100);
$c->einzahlen(50);
echo 'Eigenständiges Konto $c: ' . $c->saldo() . "\n";
echo 'Unberührtes Konto $a:    ' . $a->saldo() . "\n";

// ... oder mit clone, das eine unabhängige Kopie des Objekts erstellt.
$d = clone $a;
$d->einzahlen(1000);
echo 'Original $a nach clone: ' . $a->saldo() . "\n";
echo 'Kopie $d nach clone:    ' . $d->saldo() . "\n";
