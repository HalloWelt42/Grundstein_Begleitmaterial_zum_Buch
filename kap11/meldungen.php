<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 11: Fehler, Warnung und Hinweis unterscheiden.
 *
 * PHP meldet Probleme in Stufen. Ein Hinweis (Notice) und eine Warnung
 * (Warning) halten das Programm nicht an - es läuft weiter, aber PHP sagt
 * dir, dass etwas nicht stimmt. Ein Fehler (Error, etwa ein TypeError)
 * dagegen bricht die Ausführung ab, wenn ihn niemand abfängt. Diese Datei
 * löst bewusst eine Warnung aus und zeigt, dass das Skript danach
 * weiterläuft. Die Details der Fehlerbehandlung folgen in Teil IV.
 */

$werte = [1, 2, 3];

echo 'Vor dem Zugriff.' . PHP_EOL;

// Der Schlüssel 10 existiert nicht. PHP gibt eine Warnung aus und liefert
// null - das Programm läuft aber weiter.
echo 'Wert an Position 10: ' . $werte[10] . PHP_EOL;

echo 'Nach dem Zugriff - das Skript läuft weiter.' . PHP_EOL;
