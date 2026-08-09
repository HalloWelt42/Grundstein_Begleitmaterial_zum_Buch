<?php

declare(strict_types=1);

/**
 * Speichert Daten auf der lokalen Platte. Bringt zwei Methoden mit, deren
 * Namen sich mit dem zweiten Trait überschneiden werden.
 */
trait DateiSpeicher
{
    public function speichere(string $inhalt): string
    {
        return "Auf die Platte geschrieben: {$inhalt}";
    }

    public function ort(): string
    {
        return 'lokales Dateisystem';
    }
}

/**
 * Speichert Daten in einem entfernten Ablageort. Hat absichtlich Methoden
 * mit denselben Namen wie DateiSpeicher, damit ein Konflikt entsteht.
 */
trait CloudSpeicher
{
    public function speichere(string $inhalt): string
    {
        return "In den Ablageort geladen: {$inhalt}";
    }

    public function ort(): string
    {
        return 'entfernter Ablageort';
    }
}

/**
 * Nutzt beide Traits gleichzeitig. Weil beide eine speichere()- und eine
 * ort()-Methode mitbringen, muss der Konflikt ausdrücklich aufgelöst
 * werden - sonst bricht PHP den Ladevorgang mit einem Fatal Error ab.
 */
final class HybridSpeicher
{
    use DateiSpeicher, CloudSpeicher {
        // Bei speichere() gewinnt die Fassung aus DateiSpeicher ...
        DateiSpeicher::speichere insteadof CloudSpeicher;
        // ... die verdrängte Fassung bleibt unter neuem Namen erreichbar.
        CloudSpeicher::speichere as speichereInCloud;

        // Für ort() entscheiden wir uns umgekehrt.
        CloudSpeicher::ort insteadof DateiSpeicher;
        DateiSpeicher::ort as lokalerOrt;
    }
}

$speicher = new HybridSpeicher();

// speichere() ist die Variante aus DateiSpeicher (per insteadof gewählt).
echo $speicher->speichere('Rechnung.pdf') . "\n";
// Die verdrängte Cloud-Variante steht unter dem Aliasnamen bereit.
echo $speicher->speichereInCloud('Rechnung.pdf') . "\n";

// ort() liefert die Cloud-Fassung, der lokale Ort trägt den Aliasnamen.
echo 'Standardort: ' . $speicher->ort() . "\n";
echo 'Lokaler Ort: ' . $speicher->lokalerOrt() . "\n";
