<?php

declare(strict_types=1);

/*
 * Property Hooks - der lesende Hook (get).
 *
 * Ein get-Hook macht aus einer Eigenschaft einen berechneten Wert: Beim
 * Lesen läuft Code, statt einen gespeicherten Wert herauszugeben. Solche
 * Eigenschaften heißen "virtuell" - sie belegen keinen Speicher, sondern
 * leiten ihren Wert bei jedem Zugriff frisch aus anderen Feldern ab.
 */

/**
 * Eine Person mit Vor- und Nachnamen. Der volle Name wird nicht
 * gespeichert, sondern bei jedem Lesezugriff aus den beiden Teilen
 * zusammengesetzt.
 */
final class Person
{
    public function __construct(
        public string $vorname,
        public string $nachname,
    ) {}

    // Kurzform des get-Hooks: ein einzelner Ausdruck hinter "get =>".
    // vollName speichert nichts; der Wert entsteht bei jedem Zugriff neu.
    public string $vollName {
        get => trim("{$this->vorname} {$this->nachname}");
    }

    // Langform mit Block: mehr Platz für Zwischenschritte. Auch die
    // Initialen sind eine virtuelle Eigenschaft.
    public string $initialen {
        get {
            $ersterV = mb_substr($this->vorname, 0, 1);
            $ersterN = mb_substr($this->nachname, 0, 1);
            return mb_strtoupper($ersterV . $ersterN);
        }
    }
}

$person = new Person('Ada', 'Lovelace');

// Zugriff wie auf ein ganz normales Feld - ohne Klammern.
echo "Voller Name: {$person->vollName}\n";
echo "Initialen:   {$person->initialen}\n";

// Der berechnete Wert folgt den Quellfeldern: ändert sich der Nachname,
// ändert sich der volle Name beim nächsten Lesen automatisch mit.
$person->nachname = 'King';
echo "Nach Heirat: {$person->vollName}\n";

echo "\n";

/**
 * Ein Kreis mit einer echten, gespeicherten Eigenschaft (radius) und
 * zwei virtuellen, die sich daraus berechnen.
 */
final class Kreis
{
    public function __construct(
        public float $radius,
    ) {}

    // Fläche und Umfang speichern nichts - sie sind reine Ableitungen.
    public float $flaeche {
        get => M_PI * $this->radius ** 2;
    }

    public float $umfang {
        get => 2 * M_PI * $this->radius;
    }
}

$kreis = new Kreis(3.0);
printf("Radius:  %.2f\n", $kreis->radius);
printf("Fläche:  %.4f\n", $kreis->flaeche);
printf("Umfang:  %.4f\n", $kreis->umfang);
