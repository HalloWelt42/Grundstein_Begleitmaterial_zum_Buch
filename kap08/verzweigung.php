<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 8: Kontrollfluss
 *
 * Verzweigung mit if / elseif / else, die modernen Kurzformen (ternärer
 * Operator und Null-Koaleszenz ??) sowie die frühe Rückgabe als Mittel
 * gegen tiefe Verschachtelung. Alle Ausgaben stammen aus einem echten
 * Lauf mit PHP 8.4.
 */

// --- if / elseif / else -------------------------------------------------

$punkte = 82;

// Die Zweige werden von oben nach unten geprüft. Der erste wahre Zweig
// gewinnt, alle folgenden werden übersprungen.
if ($punkte >= 90) {
    $note = 'sehr gut';
} elseif ($punkte >= 75) {
    $note = 'gut';
} elseif ($punkte >= 60) {
    $note = 'befriedigend';
} else {
    $note = 'nicht bestanden';
}

echo "Punkte {$punkte} ergeben die Note: {$note}\n";

// --- Ternärer Operator: kurze Wenn-dann-sonst-Auswahl -------------------

$alter = 17;

// Bedingung ? Wert-wenn-wahr : Wert-wenn-falsch. Liefert einen Wert,
// den wir direkt in die Variable schreiben.
$zutritt = $alter >= 18 ? 'erlaubt' : 'verweigert';

echo "Mit {$alter} Jahren ist der Zutritt {$zutritt}.\n";

// --- Null-Koaleszenz ?? (Rückverweis auf Kapitel 6) ---------------------

$einstellungen = ['sprache' => 'de'];

// Nimm den vorhandenen Wert, sonst den Ersatz - ohne Warnung bei
// fehlendem Schlüssel.
$sprache = $einstellungen['sprache'] ?? 'en';
$thema = $einstellungen['thema'] ?? 'hell';

echo "Sprache: {$sprache}, Thema: {$thema}\n";

// --- Frühe Rückgabe statt tiefer Verschachtelung ------------------------

/**
 * Prüft den Rabatt für eine Bestellung. Statt die Prüfungen tief
 * ineinander zu schachteln, verlassen wir die Funktion früh, sobald ein
 * Fall geklärt ist. Das hält den Hauptpfad flach und gut lesbar.
 */
function rabattProzent(bool $istKunde, int $bestellwert): int
{
    // Wer nicht angemeldet ist, bekommt keinen Rabatt - sofort zurück.
    if (!$istKunde) {
        return 0;
    }

    // Kleinbestellungen ebenfalls ohne Rabatt.
    if ($bestellwert < 50) {
        return 0;
    }

    // Ab hier ist klar: Kunde mit ausreichendem Bestellwert.
    if ($bestellwert >= 200) {
        return 15;
    }

    return 10;
}

echo 'Rabatt (Gast, 300): ' . rabattProzent(false, 300) . " Prozent\n";
echo 'Rabatt (Kunde, 30): ' . rabattProzent(true, 30) . " Prozent\n";
echo 'Rabatt (Kunde, 120): ' . rabattProzent(true, 120) . " Prozent\n";
echo 'Rabatt (Kunde, 250): ' . rabattProzent(true, 250) . " Prozent\n";
