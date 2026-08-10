<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 62: Fibers als kooperative Nebenläufigkeit
 *
 * Zwei "Aufgaben" laufen scheinbar gleichzeitig, tatsächlich aber
 * nacheinander auf EINEM Kern. Jede gibt mit Fiber::suspend() freiwillig
 * ab; ein winziger Ablaufplaner setzt sie danach reihum wieder fort.
 * Das ist der Kern kooperativer Nebenläufigkeit: Es gibt kein echtes
 * Parallel, nur ein geordnetes Abwechseln an selbst gewählten Punkten.
 */

/**
 * Baut eine Aufgabe, die in mehreren Schritten "arbeitet" und nach jedem
 * Schritt freiwillig die Kontrolle abgibt.
 */
function aufgabe(string $name, int $schritte): Fiber
{
    return new Fiber(function () use ($name, $schritte): void {
        for ($i = 1; $i <= $schritte; $i++) {
            echo "{$name}: Schritt {$i} von {$schritte}" . PHP_EOL;
            Fiber::suspend(); // hier abgeben - der Planer macht bei anderen weiter
        }
        echo "{$name}: fertig" . PHP_EOL;
    });
}

echo '--- Ablaufplaner mit zwei Fibers ---' . PHP_EOL;

$aufgaben = [aufgabe('A', 3), aufgabe('B', 2)];

// Jede Fiber einmal starten - sie läuft bis zu ihrem ersten suspend().
foreach ($aufgaben as $fiber) {
    $fiber->start();
}

// Reihum fortsetzen, bis alle Aufgaben durch sind.
while ($aufgaben !== []) {
    foreach ($aufgaben as $i => $fiber) {
        $fiber->resume();

        if ($fiber->isTerminated()) {
            unset($aufgaben[$i]); // fertige Aufgabe aus dem Plan nehmen
        }
    }
}

// --- Werte zwischen Planer und Fiber hin und her ---------------------
echo PHP_EOL . '--- Werte zwischen Planer und Fiber ---' . PHP_EOL;

$rechner = new Fiber(function (): void {
    // Fiber::suspend() gibt genau den Wert zurück, den resume() hereinreicht.
    $a = Fiber::suspend('brauche erste Zahl');
    $b = Fiber::suspend('brauche zweite Zahl');
    echo "Fiber rechnet: {$a} + {$b} = " . ($a + $b) . PHP_EOL;
});

// start() liefert den Wert des ERSTEN suspend() zurück.
$frage = $rechner->start();
echo "Planer hört: {$frage}" . PHP_EOL;

// resume(20) macht 20 zum Rückgabewert des ersten suspend() und läuft bis
// zum zweiten suspend(), dessen Wert resume() zurückgibt.
$frage = $rechner->resume(20);
echo "Planer hört: {$frage}" . PHP_EOL;

// resume(22) macht 22 zum Rückgabewert des zweiten suspend() - die Fiber
// rechnet und endet.
$rechner->resume(22);
