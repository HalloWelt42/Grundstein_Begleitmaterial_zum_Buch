<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 65: Warum das u-Flag bei Umlauten Pflicht ist
 *
 * PHP arbeitet standardmäßig byteweise. Ein deutscher Umlaut belegt in
 * UTF-8 zwei Bytes. Ohne das u-Flag sieht PCRE nur Bytes; mit u-Flag deutet
 * es Muster UND Text als Folge von Zeichen. Das ändert alles, sobald
 * Umlaute im Spiel sind.
 */

$name = 'Grüße'; // G r ü ß e: 5 Zeichen, aber 7 Bytes (ü und ß je 2 Bytes)

echo 'strlen:    ' . strlen($name) . ' Bytes' . PHP_EOL;      // 7
echo 'mb_strlen: ' . mb_strlen($name) . ' Zeichen' . PHP_EOL; // 5

// Der Punkt "." trifft ohne u-Flag genau ein BYTE, mit u-Flag ein ZEICHEN.
// "Grüße" hat 7 Bytes, aber 5 Zeichen - darum kippt das Ergebnis.
$fuenfPunkteOhneU = preg_match('/^.{5}$/',  $name) === 1 ? 'ja' : 'nein';
$fuenfPunkteMitU  = preg_match('/^.{5}$/u', $name) === 1 ? 'ja' : 'nein';
echo 'genau 5x "." ohne u: ' . $fuenfPunkteOhneU . PHP_EOL;   // nein (7 Bytes)
echo 'genau 5x "." mit u:  ' . $fuenfPunkteMitU . PHP_EOL;    // ja  (5 Zeichen)

// Noch deutlicher: das erste "Zeichen" herausgreifen. Ohne u-Flag fängt
// "." nur das halbe Byte des Umlauts - das Ergebnis ist kaputt.
preg_match('/^(.)/',  'ötür', $ohne);
preg_match('/^(.)/u', 'ötür', $mit);
echo 'erstes Zeichen ohne u: 0x' . bin2hex($ohne[1]) . ' (nur ein Byte)' . PHP_EOL;
echo 'erstes Zeichen mit u:  ' . $mit[1] . PHP_EOL;

// \p{L} bedeutet "irgendein Buchstabe" und funktioniert NUR mit u-Flag.
// So werden auch Wörter mit Umlauten vollständig getroffen.
preg_match_all('/\p{L}+/u', 'Café, Straße & Tür!', $woerter);
echo 'Wörter (\p{L}+/u): ' . implode(' ', $woerter[0]) . PHP_EOL;

// Mit u-Flag deutet PCRE sogar \w (Wortzeichen) als Unicode - Umlaute
// zählen dann als Buchstabe. Ohne u zerfällt das Wort an jedem Umlaut.
preg_match_all('/\w+/',  'Grüße', $ohneW);
preg_match_all('/\w+/u', 'Grüße', $mitW);
echo '\w+ ohne u: ' . implode(' ', $ohneW[0]) . PHP_EOL; // Gr e
echo '\w+ mit u:  ' . implode(' ', $mitW[0]) . PHP_EOL;   // Grüße

// Eine Zeichenklasse mit echten Umlauten - als Validierung "nur Buchstaben".
// Ohne u-Flag würde diese Prüfung bei Umlauten falsch entscheiden.
$nurBuchstaben = '/^[a-zäöüß]+$/u';
foreach (['löwe', 'katze', 'l0we'] as $probe) {
    $urteil = preg_match($nurBuchstaben, $probe) === 1 ? 'gültig' : 'ungültig';
    echo str_pad($probe, 8) . $urteil . PHP_EOL;
}
