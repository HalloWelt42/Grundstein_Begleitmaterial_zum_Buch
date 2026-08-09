<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 26: Fehler und Ausnahmen
 *
 * Teil 3: Die Ausnahme-Hierarchie. Ganz oben steht das Interface
 * Throwable mit zwei Ästen: Error (interne Fehler und Programmierfehler
 * wie TypeError) und Exception (erwartbare Ausnahmen wie LogicException
 * und RuntimeException). Mehrere catch-Blöcke und der Union-Catch
 * (catch (A|B)) wählen gezielt aus, was gefangen wird.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/**
 * Löst je nach Kennung eine andere Art von Fehler aus, damit wir das
 * Fangen unterscheiden können.
 */
function ausloesen(string $art): void
{
    match ($art) {
        // Erwartbare Ausnahme: falsches Argument (Ast Exception).
        'argument' => throw new InvalidArgumentException('Argument passt nicht.'),
        // Erwartbare Ausnahme: erst zur Laufzeit erkennbar (Ast Exception).
        'laufzeit' => throw new RuntimeException('Etwas lief zur Laufzeit schief.'),
        // Programmierfehler: Division durch null (Ast Error).
        'teilen' => throw new DivisionByZeroError('Division durch null.'),
        default => print('Nichts passiert.' . PHP_EOL),
    };
}

// Mehrere catch-Blöcke: PHP prüft sie von oben nach unten und nimmt
// den ersten, dessen Typ passt. Der Union-Catch (A|B) fasst zwei Typen
// zusammen, die gleich behandelt werden sollen.
foreach (['argument', 'laufzeit', 'teilen'] as $art) {
    try {
        ausloesen($art);
    } catch (InvalidArgumentException | RuntimeException $fehler) {
        // Ein Ast Exception - unser Code hat einen erwartbaren Fall.
        echo 'Exception (' . $fehler::class . '): ' . $fehler->getMessage() . PHP_EOL;
    } catch (Error $fehler) {
        // Der andere Ast: ein interner Fehler bzw. Programmierfehler.
        echo 'Error (' . $fehler::class . '): ' . $fehler->getMessage() . PHP_EOL;
    }
}

echo str_repeat('-', 40) . PHP_EOL;

// Beweis der Hierarchie: Beide Äste erfüllen dasselbe Interface
// Throwable, aber nur einer ist eine Exception bzw. ein Error.
$beispiele = [
    new InvalidArgumentException('x'),
    new DivisionByZeroError('y'),
];

foreach ($beispiele as $t) {
    printf(
        "%-28s Throwable=%s Exception=%s Error=%s" . PHP_EOL,
        $t::class,
        $t instanceof Throwable ? 'ja' : 'nein',
        $t instanceof Exception ? 'ja' : 'nein',
        $t instanceof Error ? 'ja' : 'nein',
    );
}
