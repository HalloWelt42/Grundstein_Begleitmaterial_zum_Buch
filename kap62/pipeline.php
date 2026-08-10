<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Faul;

/*
 * Grundstein - Kapitel 62: Generatoren als faule Pipeline
 *
 * Drei Bausteine werden verkettet: quadrieren, die geraden behalten, die
 * ersten fünf nehmen. Die Quelle ist unendlich - ohne Generatoren
 * undenkbar. Nichts rechnet, bevor das foreach am Ende zieht, und es wird
 * nur so viel aus der Quelle gezogen, wie das Ergebnis wirklich braucht.
 */

// Eine unendliche Quelle: 1, 2, 3, ... Der Zähler per Referenz verrät uns
// hinterher, wie weit die Quelle tatsächlich laufen musste.
$gezogen = 0;
$quelle = (static function () use (&$gezogen): Generator {
    $n = 1;
    while (true) {
        $gezogen++;
        yield $n++;
    }
})();

// Die faule Pipeline: erst quadrieren, dann die geraden filtern, dann
// die ersten fünf nehmen. Bis hierher ist nichts geschehen - die
// Generatoren sind nur ineinandergesteckt.
$strom = Faul::nimm(
    Faul::filter(
        Faul::map($quelle, static fn (int $n): int => $n * $n),
        static fn (int $quadrat): bool => $quadrat % 2 === 0,
    ),
    5,
);

echo 'Erste fünf geraden Quadratzahlen: ';
foreach ($strom as $wert) {
    echo $wert . ' ';
}
echo PHP_EOL;

// Der Beweis für die Faulheit: Obwohl die Quelle unendlich ist, wurden
// nur so viele Zahlen gezogen, wie für fünf gerade Quadrate nötig waren.
echo "Aus der unendlichen Quelle gezogen: {$gezogen} Zahlen" . PHP_EOL;
