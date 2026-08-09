<?php

declare(strict_types=1);

// VORHER: ein Alleskönner. Eine Funktion prüft, summiert, rabattiert,
// formatiert und entscheidet - alles auf einmal, mit nichtssagenden Namen.
function verarbeite(array $b): string
{
    $t = '';
    if (isset($b['posten']) && count($b['posten']) > 0) {
        $s = 0;
        foreach ($b['posten'] as $p) {
            $s = $s + $p['cent'] * $p['menge'];
        }
        if ($b['stufe'] == 'gold') {
            $s = $s - intdiv($s * 20, 100);
        } else {
            if ($b['stufe'] == 'silber') {
                $s = $s - intdiv($s * 10, 100);
            }
        }
        $t = 'Summe: ' . number_format($s / 100, 2, ',', '.') . ' EUR';
    } else {
        $t = 'Keine Posten';
    }

    return $t;
}

$b1 = [
    'stufe' => 'gold',
    'posten' => [
        ['cent' => 5000, 'menge' => 2],
        ['cent' => 2500, 'menge' => 1],
    ],
];

$b2 = [
    'stufe' => 'standard',
    'posten' => [
        ['cent' => 999, 'menge' => 3],
    ],
];

$b3 = ['stufe' => 'standard', 'posten' => []];

echo verarbeite($b1) . PHP_EOL;
echo verarbeite($b2) . PHP_EOL;
echo verarbeite($b3) . PHP_EOL;
