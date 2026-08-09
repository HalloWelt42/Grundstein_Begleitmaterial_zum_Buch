<?php

declare(strict_types=1);

// Fünf winzige Ausschnitte, die je ein SOLID-Prinzip zeigen. Jeder Block
// läuft für sich; zusammen ergeben sie die Beispiele aus dem Kapitel.

// --- S: Single Responsibility ---------------------------------------
// Eine Klasse pro Grund zur Änderung: die eine rechnet, die andere gibt aus.
final class Rechnungssumme
{
    /**
     * @param list<int> $posten Beträge in Cent
     */
    public function summe(array $posten): int
    {
        return array_sum($posten);
    }
}

final class RechnungsDruck
{
    public function alsText(int $cent): string
    {
        return number_format($cent / 100, 2, ',', '.') . ' EUR';
    }
}

// --- O: Open/Closed --------------------------------------------------
// Neue Versandart? Neue Klasse, kein Eingriff in bestehenden Code.
interface Versand
{
    public function kosten(): int;
}

final class Standardversand implements Versand
{
    public function kosten(): int
    {
        return 495;
    }
}

final class Expressversand implements Versand
{
    public function kosten(): int
    {
        return 1290;
    }
}

// --- L: Liskov-Substitution -----------------------------------------
// Jede Unterart erfüllt den Vertrag, ohne ihn zu brechen: eine Fläche
// ist nie negativ - für Kreis wie Quadrat gleichermaßen.
abstract class Flaeche
{
    abstract public function wert(): float;
}

final class Quadrat extends Flaeche
{
    public function __construct(private readonly float $seite) {}

    public function wert(): float
    {
        return $this->seite ** 2;
    }
}

// --- I: Interface Segregation ---------------------------------------
// Schmale Verträge statt eines dicken: wer nur druckt, kennt nur Drucker.
interface Drucker
{
    public function drucke(string $text): void;
}

interface Scanner
{
    public function scanne(): string;
}

final class Buerodrucker implements Drucker
{
    public function drucke(string $text): void
    {
        echo "Druck: {$text}\n";
    }
}

// --- D: Dependency Inversion -----------------------------------------
// Die Klasse hängt am Vertrag Logger, nicht an einer festen Umsetzung.
interface Logger
{
    public function notiere(string $zeile): void;
}

final class EchoLogger implements Logger
{
    public function notiere(string $zeile): void
    {
        echo "[LOG] {$zeile}\n";
    }
}

final class Bestellung
{
    public function __construct(
        private readonly Logger $logger,
    ) {}

    public function aufgeben(): void
    {
        $this->logger->notiere('Bestellung aufgegeben');
    }
}

// --- Zusammenspiel zur Probe ----------------------------------------
$summe = (new Rechnungssumme())->summe([5000, 5000, 2500]);
echo (new RechnungsDruck())->alsText($summe) . "\n";

$versandarten = [new Standardversand(), new Expressversand()];
foreach ($versandarten as $art) {
    echo 'Versand: ' . number_format($art->kosten() / 100, 2, ',', '.') . " EUR\n";
}

echo 'Fläche: ' . (new Quadrat(3.0))->wert() . "\n";

(new Buerodrucker())->drucke('Beleg 2026-0042');

(new Bestellung(new EchoLogger()))->aufgeben();
