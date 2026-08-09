<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 5: Die skalaren Typen und null.
 *
 * Jeder Wert in PHP hat einen Typ. Dieses Skript legt je einen Wert
 * der vier skalaren Typen sowie den Sonderfall null an und bestimmt
 * ihren Typ. get_debug_type() ist dafür die moderne, ehrliche Wahl -
 * gettype() nennt zwei der Typen noch mit ihren alten, irreführenden
 * Namen ("integer" und "double").
 */

// --- Literale: so schreibt man Werte direkt in den Quelltext ---

$ganzzahl  = 42;            // int    - eine ganze Zahl
$kommazahl = 3.14;          // float  - eine Gleitkommazahl
$text      = 'Grundstein';  // string - eine Zeichenkette
$wahr      = true;          // bool   - Wahrheitswert (true oder false)
$nichts    = null;          // null   - der Sonderfall "kein Wert"

// get_debug_type() nennt die Typen so, wie du sie auch hinschreibst.
echo 'get_debug_type():' . PHP_EOL;
echo '  ' . get_debug_type($ganzzahl)  . PHP_EOL;   // int
echo '  ' . get_debug_type($kommazahl) . PHP_EOL;   // float
echo '  ' . get_debug_type($text)      . PHP_EOL;   // string
echo '  ' . get_debug_type($wahr)      . PHP_EOL;   // bool
echo '  ' . get_debug_type($nichts)    . PHP_EOL;   // null

// gettype() stammt aus alten Zeiten und benennt zwei Typen anders.
echo 'gettype():' . PHP_EOL;
echo '  ' . gettype($ganzzahl)  . PHP_EOL;   // integer
echo '  ' . gettype($kommazahl) . PHP_EOL;   // double (gemeint ist float!)

echo str_repeat('-', 40) . PHP_EOL;

// var_dump() zeigt Typ UND Wert zugleich - das genaueste Werkzeug,
// wenn du beim Suchen wirklich wissen willst, was in einer Variablen steckt.
var_dump($ganzzahl, $kommazahl, $text, $wahr, $nichts);
