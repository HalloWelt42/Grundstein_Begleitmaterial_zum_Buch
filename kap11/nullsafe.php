<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 11: Mit null sauber umgehen.
 *
 * null steht für die bewusste Abwesenheit eines Wertes. Drei Werkzeuge
 * machen den Umgang damit sicher und lesbar: der Nullsafe-Operator ?->
 * bricht eine Zugriffskette ab, sobald links null steht; der
 * Null-Koaleszenz-Operator ?? liefert einen Standardwert; und die frühe
 * Prüfung (guard clause) handelt den null-Fall gleich zu Beginn ab.
 */

final class Adresse
{
    public function __construct(
        public readonly string $stadt,
    ) {}
}

final class Benutzer
{
    public function __construct(
        public readonly string $name,
        public readonly ?Adresse $adresse = null,
    ) {}
}

// Zwei Benutzer: einer mit Adresse, einer ohne.
$mitAdresse  = new Benutzer('Ada', new Adresse('London'));
$ohneAdresse = new Benutzer('Grace');

// --- Nullsafe-Operator ?-> zusammen mit ?? --------------------------
// ?-> liefert null, sobald adresse null ist, statt einen Fehler zu
// werfen. ?? setzt in diesem Fall den Standardwert 'unbekannt' ein.
foreach ([$mitAdresse, $ohneAdresse] as $benutzer) {
    $stadt = $benutzer->adresse?->stadt ?? 'unbekannt';
    echo "{$benutzer->name}: {$stadt}" . PHP_EOL;
}

echo str_repeat('-', 40) . PHP_EOL;

// --- ?? bei fehlenden Array-Schlüsseln ------------------------------
// Der Zugriff auf einen fehlenden Schlüssel löst mit ?? keine Warnung
// aus, sondern liefert direkt den Standardwert.
$einstellungen = ['sprache' => 'de'];
$sprache = $einstellungen['sprache'] ?? 'en';
$thema   = $einstellungen['thema']   ?? 'hell';
echo "Sprache: {$sprache}, Thema: {$thema}" . PHP_EOL;

echo str_repeat('-', 40) . PHP_EOL;

// --- Früh prüfen (guard clause) -------------------------------------
// Den null-Fall zuerst abhandeln und aussteigen. Danach ist der Wert
// garantiert vorhanden - der übrige Code muss nicht mehr an null denken.
function begruesse(?Benutzer $benutzer): string
{
    if ($benutzer === null) {
        return 'Kein Benutzer angemeldet.';
    }

    return "Willkommen, {$benutzer->name}!";
}

echo begruesse($mitAdresse) . PHP_EOL;
echo begruesse(null) . PHP_EOL;
