<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 69: PHP 8.5 im Detail
 *
 * clone() nimmt in PHP 8.5 ein zweites Argument entgegen: ein Array aus
 * Eigenschaft => neuer Wert. Die Kopie wird erzeugt und die genannten
 * Eigenschaften werden im selben Zug überschrieben - auch readonly.
 * Nur mit php:8.5-cli ausführbar.
 */

final class Geld
{
    public function __construct(
        public readonly int $betragCent,
        public readonly string $waehrung,
    ) {
    }

    // Bisher (Kapitel 54) bauten wir eine veränderte Kopie von Hand: ein
    // neues Objekt mit allen unveränderten Feldern plus dem einen
    // geänderten. Je mehr Felder, desto mehr Wiederholung.
    public function mitBetragAlt(int $betragCent): self
    {
        return new self($betragCent, $this->waehrung);
    }

    // Mit PHP 8.5: clone() kopiert alles und überschreibt nur den Betrag.
    // Weil der Aufruf in der Klasse selbst steht, darf er die
    // readonly-Eigenschaft neu setzen.
    public function mitBetrag(int $betragCent): self
    {
        return clone($this, ['betragCent' => $betragCent]);
    }
}

$preis = new Geld(1990, 'EUR');
$teurer = $preis->mitBetrag(2490);

echo 'Original: ' . $preis->betragCent . ' ' . $preis->waehrung . PHP_EOL;
echo 'Kopie:    ' . $teurer->betragCent . ' ' . $teurer->waehrung . PHP_EOL;
echo 'Zwei Objekte: ' . ($preis === $teurer ? 'nein' : 'ja') . PHP_EOL;

echo PHP_EOL;

final class Punkt
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {
    }

    // Mehrere Eigenschaften auf einmal ersetzen.
    public function verschoben(int $dx, int $dy): self
    {
        return clone($this, [
            'x' => $this->x + $dx,
            'y' => $this->y + $dy,
        ]);
    }
}

$p = new Punkt(1, 2);
$q = $p->verschoben(10, 20);

printf('(%d,%d) verschoben -> (%d,%d)%s', $p->x, $p->y, $q->x, $q->y, PHP_EOL);
