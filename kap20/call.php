<?php

declare(strict_types=1);

/**
 * __call fängt Aufrufe nicht vorhandener Objektmethoden ab, __callStatic
 * die statischer Methoden. Beide bekommen den Namen und die Argumente als
 * Array. Sinnvoll etwa für Proxys oder schlanke Fassaden - aber ebenfalls
 * mit Bedacht, weil die Aufrufe im Editor nicht auffindbar sind.
 */
final class Protokoll
{
    /**
     * @param array<int, mixed> $argumente
     */
    public function __call(string $name, array $argumente): string
    {
        $liste = implode(', ', array_map(strval(...), $argumente));

        return "Objektaufruf {$name}({$liste})";
    }

    /**
     * @param array<int, mixed> $argumente
     */
    public static function __callStatic(string $name, array $argumente): string
    {
        return "Statischer Aufruf {$name} mit " . count($argumente) . ' Argument(en)';
    }
}

$p = new Protokoll();

// Keine dieser Methoden ist wirklich definiert.
echo $p->speichern(1, 2) . "\n";
echo Protokoll::laden('a', 'b', 'c') . "\n";
