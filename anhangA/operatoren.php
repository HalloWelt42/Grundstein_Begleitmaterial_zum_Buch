<?php

declare(strict_types=1);

// Anhang A - moderne Operatoren im Zusammenspiel.

// Null-Koaleszenz ?? : nimm den Wert, sonst den Ersatz - ohne Warnung,
// wenn der Schlüssel fehlt.
$config = ['host' => 'localhost'];
$port = $config['port'] ?? 8080;
echo "Port: {$port}\n";

// ??= weist nur zu, wenn links noch nichts (oder null) steht.
$config['host'] ??= 'ersatz';   // bleibt localhost, weil schon belegt
echo "Host: {$config['host']}\n";

// Spaceship <=> : liefert -1, 0 oder 1 - die ideale Vergleichsregel.
$zahlen = [3, 1, 2];
usort($zahlen, fn(int $a, int $b): int => $a <=> $b);
echo 'Sortiert: ' . implode(', ', $zahlen) . "\n";

// Nullsafe ?-> : bricht die Kette bei null sauber ab.
final class Konto
{
    public function __construct(public readonly ?string $inhaber = null) {}
}
$konto = null;
echo 'Inhaber: ' . ($konto?->inhaber ?? 'unbekannt') . "\n";

// Ternär ? : und Elvis ?: als knappe Auswahl.
$punkte = 60;
echo 'Ergebnis: ' . ($punkte >= 50 ? 'bestanden' : 'durchgefallen') . "\n";
$name = '' ?: 'Gast';   // '' ist "falsy" -> rechte Seite
echo "Name: {$name}\n";
