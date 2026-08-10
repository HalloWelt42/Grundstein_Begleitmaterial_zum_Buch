<?php

declare(strict_types=1);

final class Adresse
{
    public function __construct(public readonly string $stadt) {}

    public function grossbuchstaben(): string
    {
        return strtoupper($this->stadt);
    }
}

final class Kunde
{
    public function __construct(public readonly ?Adresse $adresse = null) {}
}

$kunde = new Kunde();   // ein Kunde ganz ohne Adresse

// Fehler (Warnung): eine Eigenschaft auf null lesen - Ergebnis ist null.
echo "Stadt ungeprüft: '{$kunde->adresse->stadt}'\n";

// Fehler (Error): eine Methode auf null aufrufen - Abbruch, aber fangbar.
try {
    echo $kunde->adresse->grossbuchstaben() . "\n";
} catch (\Error $fehler) {
    echo 'Error: ' . $fehler->getMessage() . "\n";
}

// Korrektur: Nullsafe ?-> bricht die Kette bei null sauber ab.
$stadt = $kunde->adresse?->stadt ?? 'unbekannt';
echo "Stadt nullsafe: {$stadt}\n";
