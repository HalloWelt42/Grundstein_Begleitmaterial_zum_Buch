<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 69: PHP 8.5 im Detail
 *
 * Das Attribut #[\NoDiscard] warnt, wenn der Rückgabewert einer Methode
 * oder Funktion ignoriert wird. Ideal für Funktionen, die nichts
 * verändern, sondern nur ein Ergebnis liefern - dessen Verwerfen also
 * fast sicher ein Fehler ist. Nur mit php:8.5-cli ausführbar.
 */

final class Konto
{
    public function __construct(private readonly int $standCent)
    {
    }

    // Die Methode ändert nichts am Objekt, sondern liefert ein neues
    // Konto. Wer das Ergebnis wegwirft, hat die Abhebung verloren.
    #[\NoDiscard('das neue Konto muss weiterverwendet werden')]
    public function abgehoben(int $betragCent): self
    {
        return new self($this->standCent - $betragCent);
    }

    public function standCent(): int
    {
        return $this->standCent;
    }
}

$konto = new Konto(10000);

// Richtig: das Ergebnis weiterverwenden.
$neu = $konto->abgehoben(2500);
echo 'Neuer Stand:   ' . $neu->standCent() . PHP_EOL;
echo 'Alter Stand:   ' . $konto->standCent() . PHP_EOL;

// Falsch: das Ergebnis ignorieren - PHP 8.5 gibt hier eine Warnung aus.
$konto->abgehoben(2500);

// Bewusst verwerfen: der (void)-Cast macht die Absicht sichtbar und
// unterdrückt die Warnung.
(void) $konto->abgehoben(2500);
