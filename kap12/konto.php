<?php

declare(strict_types=1);

/**
 * Ein einfaches Bankkonto. Jedes Objekt dieser Klasse hält seinen eigenen
 * Kontostand und den Namen des Inhabers.
 */
final class Konto
{
    // Typisierte Eigenschaften: jedes Konto-Objekt bekommt eigene Werte.
    private string $inhaber;
    private int $saldo;

    /**
     * Der Konstruktor legt ein neues Konto an. $this verweist auf das
     * gerade entstehende Objekt; der Startsaldo ist optional.
     */
    public function __construct(string $inhaber, int $startSaldo = 0)
    {
        $this->inhaber = $inhaber;
        $this->saldo = $startSaldo;
    }

    /**
     * Zahlt einen Betrag ein und erhöht damit den Kontostand.
     */
    public function einzahlen(int $betrag): void
    {
        $this->saldo += $betrag;
    }

    /**
     * Hebt einen Betrag ab, sofern er gedeckt ist. Liefert true bei
     * Erfolg und false, wenn das Guthaben nicht reicht.
     */
    public function auszahlen(int $betrag): bool
    {
        if ($betrag > $this->saldo) {
            return false;
        }
        $this->saldo -= $betrag;
        return true;
    }

    /**
     * Liefert den aktuellen Kontostand.
     */
    public function saldo(): int
    {
        return $this->saldo;
    }

    /**
     * Liefert den Namen des Inhabers.
     */
    public function inhaber(): string
    {
        return $this->inhaber;
    }
}

// Zwei unabhängige Objekte aus derselben Klasse.
$konto1 = new Konto('Ada', 100);
$konto2 = new Konto('Grace');

$konto1->einzahlen(50);
$konto2->einzahlen(20);
$konto1->auszahlen(30);

echo $konto1->inhaber() . ': ' . $konto1->saldo() . " Euro\n";
echo $konto2->inhaber() . ': ' . $konto2->saldo() . " Euro\n";

// Eine nicht gedeckte Auszahlung wird abgelehnt.
$erfolg = $konto2->auszahlen(500);
echo 'Auszahlung über 500 bei Grace: ' . ($erfolg ? 'ausgeführt' : 'abgelehnt') . "\n";
echo $konto2->inhaber() . ': ' . $konto2->saldo() . " Euro\n";
