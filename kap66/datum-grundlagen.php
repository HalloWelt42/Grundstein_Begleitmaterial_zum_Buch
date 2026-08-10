<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Erzeugen und Formatieren mit DateTimeImmutable - und der Beweis der
 * Unveränderlichkeit: modify() ändert das Original nie, sondern liefert
 * ein neues Objekt zurück.
 */

// --- Erzeugen -------------------------------------------------------
// Aus einer Zeichenkette, ausdrücklich in UTC. Ohne Zonenangabe würde
// PHP die eingestellte Standardzone nehmen - das ist selten gewollt.
$start = new DateTimeImmutable('2026-03-28 09:30:00', new DateTimeZone('UTC'));

echo '--- Formatieren ---' . PHP_EOL;
echo $start->format('Y-m-d H:i:s') . PHP_EOL;         // maschinenlesbar
echo $start->format('l, d F Y') . PHP_EOL;            // Namen: englisch!
echo $start->format(DateTimeImmutable::ATOM) . PHP_EOL; // ISO 8601 mit Zone

echo PHP_EOL . '--- Unveränderlichkeit ---' . PHP_EOL;
// modify() gibt ein NEUES Objekt zurück; $start selbst bleibt gleich.
$spaeter = $start->modify('+2 hours');
echo 'Original: ' . $start->format('H:i') . PHP_EOL;
echo 'Später:   ' . $spaeter->format('H:i') . PHP_EOL;
echo 'Gleich?   ' . ($start === $spaeter ? 'ja' : 'nein, zwei Objekte') . PHP_EOL;
