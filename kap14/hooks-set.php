<?php

declare(strict_types=1);

/*
 * Property Hooks - der schreibende Hook (set).
 *
 * Ein set-Hook fängt den Schreibzugriff auf eine Eigenschaft ab. Dort
 * kannst du den Wert prüfen (Validierung) oder in eine einheitliche Form
 * bringen (Normalisierung), bevor er gespeichert wird. Eine Eigenschaft
 * mit set-Hook, die ihren Wert weiterhin ablegt, bleibt eine echte,
 * gespeicherte Eigenschaft ("backed property").
 */

/**
 * Ein Benutzerkonto. Die E-Mail-Adresse wird beim Schreiben normalisiert
 * (klein, ohne Rand-Leerzeichen) und grob geprüft.
 */
final class Benutzer
{
    // Backed property: der set-Hook formt den Wert und legt ihn dann
    // im Feld ab. Beim Lesen kommt der gespeicherte, normalisierte Wert
    // heraus - ganz ohne get-Hook.
    public string $email {
        set (string $value) {
            $sauber = strtolower(trim($value));
            if (!str_contains($sauber, '@')) {
                throw new InvalidArgumentException(
                    "Keine gültige E-Mail: {$value}"
                );
            }
            // Zuweisung an $this->email im set-Hook schreibt in den
            // Speicher, ohne den Hook erneut auszulösen.
            $this->email = $sauber;
        }
    }

    // Kurzform des set-Hooks: der Ausdruck hinter "set =>" wird
    // gespeichert. $value ist der eingehende Wert.
    public string $anzeigename {
        set => trim($value);
    }

    public function __construct(string $email, string $anzeigename)
    {
        // Diese Zuweisungen laufen durch die set-Hooks.
        $this->email = $email;
        $this->anzeigename = $anzeigename;
    }
}

// Eingaben mit Großbuchstaben und Leerzeichen werden geglättet.
$benutzer = new Benutzer('  Ada.Lovelace@Example.COM  ', '  Ada  ');
echo "E-Mail:      {$benutzer->email}\n";
echo "Anzeigename: [{$benutzer->anzeigename}]\n";

// Auch eine spätere Zuweisung läuft durch den Hook.
$benutzer->email = 'GRACE@navy.MIL';
echo "Neue E-Mail: {$benutzer->email}\n";

// Eine ungültige Adresse wird abgewiesen.
try {
    $benutzer->email = 'keine-adresse';
} catch (InvalidArgumentException $e) {
    echo "Abgewiesen:  {$e->getMessage()}\n";
}

echo "\n";

/**
 * Ein Konto, dessen Stand nie negativ werden darf. Der set-Hook wacht
 * über jede Zuweisung - egal, von wo sie kommt.
 */
final class Konto
{
    public int $stand {
        set (int $value) {
            if ($value < 0) {
                throw new InvalidArgumentException(
                    "Der Stand darf nicht negativ sein: {$value}"
                );
            }
            $this->stand = $value;
        }
    }

    public function __construct(int $start)
    {
        $this->stand = $start;
    }
}

$konto = new Konto(100);
$konto->stand = 40;
echo "Stand: {$konto->stand}\n";

try {
    $konto->stand = -10;
} catch (InvalidArgumentException $e) {
    echo "Abgewiesen: {$e->getMessage()}\n";
}
