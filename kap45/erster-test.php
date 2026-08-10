<?php

declare(strict_types=1);

require __DIR__ . '/src/Preis.php';

use App\Preis;

/**
 * Ein winziger Test-Rahmen von Hand. Er vergleicht einen erwarteten mit
 * einem tatsächlichen Wert, meldet grün oder rot und gibt bei einem
 * Fehlschlag beide Werte aus. Genau diese Idee steckt später in jedem
 * echten Test-Framework - nur ausgereifter.
 */
function pruefe(string $name, mixed $erwartet, mixed $tatsaechlich): bool
{
    $bestanden = $erwartet === $tatsaechlich;
    echo sprintf('  [%-6s] %s', $bestanden ? 'OK' : 'FEHLER', $name) . PHP_EOL;

    if (!$bestanden) {
        echo sprintf(
            '           erwartet: %s, war: %s',
            var_export($erwartet, true),
            var_export($tatsaechlich, true),
        ) . PHP_EOL;
    }

    return $bestanden;
}

/**
 * Prüft, dass der Code eine bestimmte Ausnahme wirft. Ohne diesen Fall
 * bliebe das Fehlerverhalten - ein Teil des Verhaltens - ungetestet.
 */
function pruefeAusnahme(string $name, string $erwarteteKlasse, callable $code): bool
{
    try {
        $code();
    } catch (\Throwable $fehler) {
        return pruefe($name, $erwarteteKlasse, $fehler::class);
    }

    return pruefe($name . ' (es wurde keine Ausnahme geworfen)', $erwarteteKlasse, 'keine');
}

// --- Die eigentlichen Prüfungen --------------------------------------
$ergebnisse = [];

$ergebnisse[] = pruefe(
    '20 % Rabatt auf 10,00 EUR ergeben 8,00 EUR',
    800,
    (new Preis(1000))->mitRabatt(20)->cent,
);

$ergebnisse[] = pruefe(
    '0 % Rabatt lässt den Preis unverändert',
    1000,
    (new Preis(1000))->mitRabatt(0)->cent,
);

$ergebnisse[] = pruefe(
    'alsEuro formatiert 1234 Cent als deutschen Text',
    '12,34',
    (new Preis(1234))->alsEuro(),
);

// Verhalten heißt auch: Der ursprüngliche Preis bleibt unangetastet.
$original = new Preis(1000);
$original->mitRabatt(50);
$ergebnisse[] = pruefe(
    'mitRabatt verändert den ursprünglichen Preis nicht',
    1000,
    $original->cent,
);

$ergebnisse[] = pruefeAusnahme(
    'ein negativer Preis wird abgelehnt',
    \InvalidArgumentException::class,
    static fn (): Preis => new Preis(-1),
);

// --- Zusammenfassung: grün nur, wenn wirklich alles bestanden ist -----
$fehlgeschlagen = count(array_filter($ergebnisse, static fn (bool $ok): bool => !$ok));

echo PHP_EOL;
if ($fehlgeschlagen === 0) {
    echo sprintf('grün: alle %d Prüfungen bestanden.', count($ergebnisse)) . PHP_EOL;
    exit(0);
}

echo sprintf('rot: %d von %d Prüfungen fehlgeschlagen.', $fehlgeschlagen, count($ergebnisse)) . PHP_EOL;
exit(1);
