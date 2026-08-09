<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 5: Typumwandlung, explizit und implizit.
 *
 * Explizites Casting mit (int), (float), (string), (bool) sagt klar an,
 * dass eine Umwandlung gewollt ist. Daneben wandelt PHP bei manchen
 * Operatoren auch von selbst um (implizit) - und beides hat Tücken.
 * Der strikte Modus ändert an den Operatoren übrigens nichts: (int) und
 * das + unten verhalten sich mit und ohne declare(strict_types=1) gleich.
 */

// --- Explizit nach int wandeln ---
var_dump((int) '42');       // 42   - sauberer Zahlentext
var_dump((int) '12abc');    // 12   - liest die führende Zahl, Rest ignoriert
var_dump((int) 'abc');      // 0    - kein Zahlanfang -> 0
var_dump((int) 3.9);        // 3    - float wird ABGESCHNITTEN, nicht gerundet

echo str_repeat('-', 40) . PHP_EOL;

// --- Explizit nach string wandeln ---
var_dump((string) 42);      // "42"
var_dump((string) true);    // "1"    - true wird zu "1"
var_dump((string) false);   // ""     - false wird zum LEEREN String
var_dump((string) null);    // ""     - null ebenfalls zum leeren String

echo str_repeat('-', 40) . PHP_EOL;

// --- Implizit: Operatoren wandeln bei Bedarf selbst um ---
var_dump('10' + 5);         // int(15)    - der Zahlentext wird zur Zahl
var_dump('10' . 5);         // "105"      - der Punkt verkettet zu Text
var_dump(true + 1);         // int(2)     - true zählt beim Rechnen als 1
var_dump(null + 1);         // int(1)     - null zählt beim Rechnen als 0
