<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 7: Zeichenketten
 *
 * Zeigt einfache und doppelte Anführungszeichen, Interpolation mit
 * geschweiften Klammern, Heredoc und Nowdoc, die Verkettung sowie die
 * wichtigsten String-Funktionen. Alle Ausgaben stammen aus einem echten
 * Lauf mit PHP 8.4.
 */

// --- Einfache vs. doppelte Anführungszeichen ----------------------------

$name = 'Ada';

// Einfach: alles steht wortwörtlich da. $name wird NICHT ersetzt,
// und \n bleibt ein Backslash gefolgt von einem n.
echo 'Einfach: Hallo $name, Ende\n' . "\n";

// Doppelt: PHP setzt den Wert von $name ein und deutet \n als Umbruch.
echo "Doppelt: Hallo $name, Ende\n";

// --- Interpolation mit geschweiften Klammern ----------------------------

$anzahl = 3;
$person = ['vorname' => 'Grace', 'nachname' => 'Hopper'];

// Die Klammern grenzen den Ausdruck sauber ab. Beim Zugriff auf einen
// Array-Schlüssel sind sie in dieser Form Pflicht.
echo "\n";
echo "Es sind {$anzahl} Namen in der Liste.\n";
echo "Angemeldet: {$person['vorname']} {$person['nachname']}.\n";

// --- Heredoc und Nowdoc -------------------------------------------------

$titel = 'Grundstein';

// Heredoc verhält sich wie doppelte Anführungszeichen: es interpoliert.
// Die Einrückung der Schlussmarke wird von jeder Zeile abgezogen (ab 7.3).
$heredoc = <<<TEXT
    Willkommen bei {$titel}.
    Diese Zeilen behalten ihre Umbrüche.
    TEXT;

// Nowdoc verhält sich wie einfache Anführungszeichen: nichts wird ersetzt.
$nowdoc = <<<'TEXT'
    Hier bleibt {$titel} wörtlich stehen.
    TEXT;

echo "\n";
echo $heredoc . "\n";
echo $nowdoc . "\n";

// --- Verkettung ---------------------------------------------------------

$vorname = 'Alan';
$nachname = 'Turing';

// Der Punkt verbindet zwei Zeichenketten. .= hängt rechts an eine
// bestehende Zeichenkette an.
$vollname = $vorname . ' ' . $nachname;

$gruss = 'Hallo, ';
$gruss .= $vollname;
$gruss .= '!';

echo "\n";
echo $vollname . "\n";
echo $gruss . "\n";

// --- Die wichtigsten String-Funktionen ----------------------------------

$satz = '  Modernes PHP macht Freude  ';

echo "\n";

// Länge in BYTES (Vorsicht bei Umlauten - siehe mb.php).
echo 'Länge roh: ' . strlen($satz) . "\n";

// Rand-Leerzeichen entfernen; wir zeigen die Grenzen in eckigen Klammern.
$sauber = trim($satz);
echo "Getrimmt: [{$sauber}]\n";

// Enthält / beginnt mit / endet mit (PHP 8.0+) - liefern einen bool.
$hatPhp = str_contains($sauber, 'PHP');
echo 'Enthält PHP: ' . ($hatPhp ? 'ja' : 'nein') . "\n";
echo 'Beginnt mit Modern: ' . (str_starts_with($sauber, 'Modern') ? 'ja' : 'nein') . "\n";

// Ersetzen: jedes Vorkommen des Suchworts wird ausgetauscht.
$ersetzt = str_replace('Freude', 'Spaß', $sauber);
echo "Ersetzt: {$ersetzt}\n";

// Ausschnitt: ab Position 0, acht Zeichen lang (byteweise).
echo 'Ausschnitt: ' . substr($sauber, 0, 8) . "\n";

// Groß- und Kleinschreibung (nur ASCII sicher - Umlaute siehe mb.php).
echo 'Klein: ' . strtolower($sauber) . "\n";
echo 'Groß: ' . strtoupper($sauber) . "\n";
echo 'Erster Buchstabe groß: ' . ucfirst('hallo welt') . "\n";
