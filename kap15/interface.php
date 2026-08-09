<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 15: Vererbung, abstrakte Klassen und Interfaces
 *
 * Teil 3: Interfaces als Vertrag. Ein Interface schreibt nur vor, WELCHE
 * Methoden es geben muss - ohne jeden Rumpf. Eine Klasse verspricht mit
 * implements, den Vertrag zu erfüllen. Eine Klasse darf mehrere Verträge
 * zugleich erfüllen, was mit einfacher Vererbung nicht ginge.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

// --- Zwei Verträge -----------------------------------------------------

/**
 * Vertrag: Wer das erfüllt, lässt sich als Array darstellen - etwa zum
 * späteren Umwandeln in JSON.
 */
interface Darstellbar
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

/**
 * Vertrag: Wer das erfüllt, liefert einen Ordnungswert und kann sich
 * darüber mit einem gleichartigen Objekt vergleichen. Rückgabe von
 * vergleicheMit() ist negativ, 0 oder positiv.
 */
interface Vergleichbar
{
    /**
     * Der Wert, nach dem verglichen und sortiert wird.
     */
    public function ordnungswert(): int;

    public function vergleicheMit(Vergleichbar $andere): int;
}

// --- Eine Klasse erfüllt beide Verträge --------------------------------

/**
 * Ein Geldbetrag in Cent. Er ist sowohl darstellbar als auch mit anderen
 * Beträgen vergleichbar - deshalb implementiert er beide Interfaces.
 */
final class Geldbetrag implements Darstellbar, Vergleichbar
{
    public function __construct(
        private readonly int $cent,
        private readonly string $waehrung = 'EUR',
    ) {}

    public function toArray(): array
    {
        return [
            'cent' => $this->cent,
            'waehrung' => $this->waehrung,
        ];
    }

    public function ordnungswert(): int
    {
        return $this->cent;
    }

    public function vergleicheMit(Vergleichbar $andere): int
    {
        // Der Vergleich läuft über den vertraglich zugesicherten
        // Ordnungswert, nie über interne Felder der anderen Klasse.
        return $this->ordnungswert() <=> $andere->ordnungswert();
    }

    public function alsText(): string
    {
        return sprintf('%.2f %s', $this->cent / 100, $this->waehrung);
    }
}

$a = new Geldbetrag(1299);
$b = new Geldbetrag(950);

// Weil Geldbetrag den Vertrag Darstellbar erfüllt, können wir uns auf
// toArray() verlassen, ohne die Klasse im Detail zu kennen.
echo json_encode($a->toArray()) . "\n";

// Und weil er Vergleichbar erfüllt, funktioniert eine Sortierung.
$betraege = [$a, $b, new Geldbetrag(1299), new Geldbetrag(50)];
usort($betraege, fn (Vergleichbar $x, Vergleichbar $y): int => $x->vergleicheMit($y));

$texte = array_map(fn (Geldbetrag $g): string => $g->alsText(), $betraege);
echo 'Sortiert: ' . implode(', ', $texte) . "\n";

// Der Typ-Check greift auf den Vertrag, nicht auf die konkrete Klasse.
function beschreibe(Darstellbar $d): void
{
    echo 'Als Array: ' . json_encode($d->toArray()) . "\n";
}

beschreibe($b);
