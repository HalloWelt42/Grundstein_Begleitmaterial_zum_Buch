<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 67: Internationalisierung
 *
 * MessageFormatter füllt Platzhalter und - viel wichtiger - wählt die
 * grammatisch richtige Pluralform. Die naive Annahme "bei 1 Einzahl, sonst
 * Mehrzahl" gilt nur für wenige Sprachen; andere kennen mehr als zwei Formen.
 */

// Muster mit einem Zahl-Platzhalter n und einer plural-Auswahl. ICU wählt
// anhand von n die passende Kategorie (=0, one, other ...); die Raute # setzt
// die Zahl selbst ein.
$musterDe = '{n, plural, =0 {Keine neuen Dateien} one {# neue Datei} other {# neue Dateien}}';

echo '--- Deutsch: null, eine, mehrere ---' . PHP_EOL;
foreach ([0, 1, 2, 5] as $n) {
    echo MessageFormatter::formatMessage('de-DE', $musterDe, ['n' => $n]) . PHP_EOL;
}

// Polnisch kennt neben "one" die Kategorien "few" und "many" - eine naive
// Zwei-Formen-Logik läge hier bei fast jeder Zahl daneben. Das Beispielwort
// "godzina" (Stunde) hat in allen Formen nur ASCII-Buchstaben.
$musterPl = '{n, plural, one {# godzina} few {# godziny} many {# godzin} other {# godziny}}';

echo PHP_EOL . '--- Polnisch: mehr als zwei Formen ---' . PHP_EOL;
foreach ([1, 2, 5, 22] as $n) {
    echo MessageFormatter::formatMessage('pl-PL', $musterPl, ['n' => $n]) . PHP_EOL;
}

// Platzhalter lassen sich mischen: ein Name als Text, eine Anzahl mit
// Pluralwahl - beide im selben Muster.
$muster = '{name} hat {n, plural, one {eine neue Nachricht} other {# neue Nachrichten}}.';

echo PHP_EOL . '--- Gemischte Platzhalter (de-DE) ---' . PHP_EOL;
echo MessageFormatter::formatMessage('de-DE', $muster, ['name' => 'Ada', 'n' => 1]) . PHP_EOL;
echo MessageFormatter::formatMessage('de-DE', $muster, ['name' => 'Ada', 'n' => 3]) . PHP_EOL;
