<?php

declare(strict_types=1);

/**
 * Ein Bankkonto schützt seinen Kontostand. Von außen ist der Saldo nicht
 * direkt erreichbar - Geld bewegt sich nur über die geprüften Methoden
 * einzahlen() und abheben(). Genau das ist Kapselung.
 */
class Konto
{
    // private: nur innerhalb dieser Klasse sichtbar. Der Saldo lässt sich
    // von außen weder lesen noch beschreiben.
    private float $saldo = 0.0;

    // protected: sichtbar in dieser Klasse UND in abgeleiteten Klassen,
    // aber nicht von außen. Der Inhaber gehört zum inneren Bauplan.
    protected string $inhaber;

    public function __construct(string $inhaber)
    {
        $this->inhaber = $inhaber;
    }

    /**
     * Zahlt einen Betrag ein. Negative Beträge lehnt die Methode ab -
     * eine Prüfung, die ein direkter Feldzugriff nie erzwingen könnte.
     */
    public function einzahlen(float $betrag): void
    {
        if ($betrag <= 0.0) {
            throw new InvalidArgumentException('Betrag muss positiv sein.');
        }
        $this->saldo += $betrag;
    }

    /**
     * Hebt einen Betrag ab, aber nur wenn Deckung besteht.
     */
    public function abheben(float $betrag): void
    {
        if ($betrag > $this->saldo) {
            throw new RuntimeException('Nicht genug Guthaben.');
        }
        $this->saldo -= $betrag;
    }

    // Ein lesender Zugang zum geschützten Saldo. Nach außen nur lesbar.
    public function getSaldo(): float
    {
        return $this->saldo;
    }
}

$konto = new Konto('Ada Lovelace');
$konto->einzahlen(100.0);
$konto->abheben(30.0);

printf("Saldo: %.2f Euro\n", $konto->getSaldo());

// Der Versuch, den Saldo direkt zu setzen, wird abgefangen: die
// Eigenschaft ist private und außerhalb der Klasse nicht erreichbar.
try {
    $konto->saldo = 1000000.0;
} catch (Error $e) {
    echo 'Direkter Zugriff verweigert: ' . $e->getMessage() . "\n";
}
