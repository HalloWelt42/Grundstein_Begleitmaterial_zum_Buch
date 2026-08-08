<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 3: Erster Versuch, HTML aus PHP zu erzeugen.
 *
 * HTML per echo funktioniert, wird aber schnell mühsam: Jede Zeile ist ein
 * String, Anführungszeichen müssen mit Backslash geschützt werden, und
 * zwischen Text und Werten steht ständig der Punkt zum Verketten. Genau
 * diese Reibung lösen wir im Buch danach mit eingebettetem PHP auf.
 */

// Aktuelle Stunde als Zahl von 0 bis 23.
$stunde = (int) date('G');

// Vor 18 Uhr "Guten Tag", danach "Guten Abend" (ternärer Operator).
$begruessung = $stunde < 18 ? 'Guten Tag' : 'Guten Abend';

echo "<!DOCTYPE html>\n";
echo "<html lang=\"de\">\n";
echo "<body>\n";
echo "    <h1>" . $begruessung . "!</h1>\n";
echo "</body>\n";
echo "</html>\n";
