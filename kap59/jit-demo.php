<?php

declare(strict_types=1);

/*
 * Ein rechenlastiger Kern ohne Ein- und Ausgabe: Er zählt, wie viele Punkte
 * eines Gitters zur Mandelbrot-Menge gehören. Das ist reine
 * Gleitkomma-Arithmetik in einer engen Schleife - genau die Art Code, bei der
 * der JIT etwas ausrichten kann. Derselbe Kern wird einmal mit und einmal
 * ohne JIT gestartet (über Kommandozeilen-Schalter), um den Unterschied zu
 * zeigen.
 */

/**
 * Zählt die Gitterpunkte, die nach $maxIter Schritten noch in der
 * Mandelbrot-Menge liegen. Nur Zahlen, keine Arrays, keine Funktionsaufrufe
 * im inneren Kern - ideal für den JIT.
 */
function mandelbrotPunkte(int $breite, int $hoehe, int $maxIter): int
{
    $treffer = 0;

    for ($py = 0; $py < $hoehe; $py++) {
        $y0 = ($py / $hoehe) * 2.0 - 1.0;

        for ($px = 0; $px < $breite; $px++) {
            $x0 = ($px / $breite) * 3.0 - 2.0;

            $x = 0.0;
            $y = 0.0;
            $iter = 0;

            while ($x * $x + $y * $y <= 4.0 && $iter < $maxIter) {
                $xNeu = $x * $x - $y * $y + $x0;
                $y = 2.0 * $x * $y + $y0;
                $x = $xNeu;
                $iter++;
            }

            if ($iter === $maxIter) {
                $treffer++;
            }
        }
    }

    return $treffer;
}

// Ist der JIT gerade aktiv? opcache_get_status() gibt es nur, wenn OPcache
// geladen ist, und liefert false, wenn es abgeschaltet ist.
$status = function_exists('opcache_get_status') ? opcache_get_status(false) : false;
$jitAn = is_array($status) && (bool) ($status['jit']['on'] ?? false);

$start = hrtime(true);
$treffer = mandelbrotPunkte(1000, 750, 1000);
$dauer = (hrtime(true) - $start) / 1_000_000; // ms

printf('JIT aktiv:  %s%s', $jitAn ? 'ja' : 'nein', PHP_EOL);
printf('Punkte:     %d%s', $treffer, PHP_EOL);
printf('Dauer:      %.1f ms%s', $dauer, PHP_EOL);
