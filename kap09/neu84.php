<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 9: Neu in PHP 8.4 - array_find, array_find_key,
 * array_any und array_all.
 *
 * Vier neue Funktionen beantworten wiederkehrende Fragen an eine Liste in
 * einer Zeile. Alle bekommen ein Array und eine Rückruffunktion, die für
 * jedes Element einen bool liefert. Alle Ausgaben stammen aus einem echten
 * Lauf mit PHP 8.4.
 */

$nutzer = [
    ['name' => 'Ada',   'alter' => 36, 'aktiv' => true],
    ['name' => 'Grace', 'alter' => 41, 'aktiv' => true],
    ['name' => 'Linus', 'alter' => 24, 'aktiv' => false],
];

// array_find: der erste passende WERT (oder null).
$ersterUnter30 = array_find($nutzer, fn (array $n): bool => $n['alter'] < 30);
echo 'Erster unter 30: ' . ($ersterUnter30['name'] ?? 'niemand') . "\n";

// array_find_key: der SCHLÜSSEL des ersten Treffers.
$position = array_find_key($nutzer, fn (array $n): bool => $n['name'] === 'Grace');
echo "Grace steht an Position: {$position}\n";

// array_any: gibt es MINDESTENS EINEN Treffer?
$gibtInaktive = array_any($nutzer, fn (array $n): bool => $n['aktiv'] === false);
echo 'Gibt es inaktive Nutzer: ' . ($gibtInaktive ? 'ja' : 'nein') . "\n";

// array_all: passen ALLE Elemente?
$alleVolljaehrig = array_all($nutzer, fn (array $n): bool => $n['alter'] >= 18);
echo 'Alle volljährig: ' . ($alleVolljaehrig ? 'ja' : 'nein') . "\n";
