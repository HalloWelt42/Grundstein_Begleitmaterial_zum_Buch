<?php

declare(strict_types=1);

/*
 * Durchgehendes Beispiel: eine Temperatur.
 *
 * celsius ist eine echte, gespeicherte Eigenschaft mit einem set-Hook,
 * der jeden Wert gegen den absoluten Nullpunkt prüft. fahrenheit ist
 * dagegen rein virtuell: get und set rechnen nur um - gespeichert wird
 * immer nur celsius. So gibt es genau eine Quelle der Wahrheit, und die
 * beiden Skalen können nie auseinanderlaufen.
 */
final class Temperatur
{
    // Kälter als der absolute Nullpunkt ist physikalisch unmöglich.
    private const float NULLPUNKT = -273.15;

    // Backed property mit Validierung: der Wert wird geprüft und dann
    // gespeichert. Lesen liefert den gespeicherten Wert.
    public float $celsius {
        set (float $value) {
            if ($value < self::NULLPUNKT) {
                throw new InvalidArgumentException(
                    "Unter dem absoluten Nullpunkt: {$value} Grad Celsius"
                );
            }
            $this->celsius = $value;
        }
    }

    // Virtuelle Eigenschaft: kein eigener Speicher. get rechnet aus
    // celsius heraus, set rechnet in celsius zurück - und läuft dabei
    // durch dessen Validierung.
    public float $fahrenheit {
        get => $this->celsius * 9 / 5 + 32;
        set (float $value) {
            $this->celsius = ($value - 32) * 5 / 9;
        }
    }

    // Rein lesende, virtuelle Eigenschaft für eine kurze Einordnung.
    public string $zustand {
        get => match (true) {
            $this->celsius <= 0.0   => 'gefroren',
            $this->celsius >= 100.0 => 'kochend',
            default                 => 'flüssig',
        };
    }

    public function __construct(float $celsius)
    {
        // Läuft durch den set-Hook von celsius - inklusive Prüfung.
        $this->celsius = $celsius;
    }
}

$wasser = new Temperatur(20.0);
printf("Start:   %.1f Grad C = %.1f Grad F (%s)\n",
    $wasser->celsius, $wasser->fahrenheit, $wasser->zustand);

// Schreiben in die virtuelle Skala verändert in Wahrheit celsius.
$wasser->fahrenheit = 212.0;
printf("Gekocht: %.1f Grad C = %.1f Grad F (%s)\n",
    $wasser->celsius, $wasser->fahrenheit, $wasser->zustand);

$wasser->celsius = -5.0;
printf("Gefroren: %.1f Grad C = %.1f Grad F (%s)\n",
    $wasser->celsius, $wasser->fahrenheit, $wasser->zustand);

// Ein unmöglicher Wert wird schon beim Schreiben abgewiesen.
try {
    $wasser->celsius = -300.0;
} catch (InvalidArgumentException $e) {
    echo "Abgewiesen: {$e->getMessage()}\n";
}
