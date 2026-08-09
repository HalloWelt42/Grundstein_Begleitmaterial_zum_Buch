<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 26: Fehler und Ausnahmen
 *
 * Teil 2: Die drei Bausteine try, catch und finally, dazu throw als
 * Ausdruck. Der try-Block enthält den Code, der schiefgehen kann; der
 * catch-Block fängt eine geworfene Ausnahme; der finally-Block läuft
 * immer - ob es gutging oder nicht - und eignet sich zum Aufräumen.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/**
 * Liest eine ganze Zahl aus einem Text. Ist der Text keine gültige
 * Zahl, wirft die Funktion eine Ausnahme statt still 0 zu liefern.
 */
function leseZahl(string $text): int
{
    if (!ctype_digit($text)) {
        throw new InvalidArgumentException("Keine gültige Zahl: {$text}");
    }

    return (int) $text;
}

// try/catch/finally im Zusammenspiel. Der finally-Block läuft in
// beiden Fällen - egal ob der try-Block sauber durchläuft oder ob
// der catch-Block eine Ausnahme behandelt hat.
foreach (['42', 'zwölf'] as $eingabe) {
    try {
        $zahl = leseZahl($eingabe);
        echo "Gelesen: {$zahl}" . PHP_EOL;
    } catch (InvalidArgumentException $fehler) {
        echo 'Abgefangen: ' . $fehler->getMessage() . PHP_EOL;
    } finally {
        echo "Fertig mit Eingabe '{$eingabe}'." . PHP_EOL;
    }
}

echo str_repeat('-', 40) . PHP_EOL;

/**
 * Liefert die Zeitzone zu einer Stadt oder wirft eine Ausnahme, wenn
 * die Stadt unbekannt ist. Praktisch für throw als Ausdruck.
 *
 * @param array<string, string> $tabelle
 */
function zeitzone(array $tabelle, string $stadt): string
{
    // throw ist ein AUSDRUCK: Es darf rechts von ?? stehen. Fehlt der
    // Schlüssel, greift die Null-Koaleszenz und wirft die Ausnahme.
    return $tabelle[$stadt]
        ?? throw new RuntimeException("Unbekannte Stadt: {$stadt}");
}

$zonen = [
    'Buxtehude' => 'Europe/Berlin',
    'Tokio' => 'Asia/Tokyo',
];

echo zeitzone($zonen, 'Tokio') . PHP_EOL;

// throw auch als Ausdruck in einem match-Arm: Der Standardfall wirft,
// wenn keiner der aufgezählten Fälle passt.
foreach (['rot', 'blau'] as $stufe) {
    try {
        $farbe = match ($stufe) {
            'rot' => 'Gefahr',
            'grün' => 'alles gut',
            default => throw new InvalidArgumentException("Unbekannte Stufe: {$stufe}"),
        };
        echo "Stufe {$stufe}: {$farbe}" . PHP_EOL;
    } catch (InvalidArgumentException $fehler) {
        echo 'Abgefangen: ' . $fehler->getMessage() . PHP_EOL;
    }
}
