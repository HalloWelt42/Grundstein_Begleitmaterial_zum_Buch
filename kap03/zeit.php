<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 3: Die erste dynamische Seite.
 *
 * Diese Datei ist ein vollständiges HTML-Dokument, in das an wenigen
 * Stellen PHP eingebettet ist. Der Kopf berechnet die Werte (Logik), der
 * Rumpf gibt sie aus (Darstellung). So bleiben beide Welten getrennt und
 * das HTML bleibt gut lesbar.
 *
 * Hinweis: date() nutzt die Zeitzone des Servers. Ohne eigene Einstellung
 * ist das UTC; wie man die Zeitzone sauber in der php.ini setzt, zeigt das
 * nächste Kapitel.
 */

// --- Logik: einmal oben alles vorbereiten -------------------------------

// Die aktuelle Stunde als ganze Zahl von 0 bis 23 (Format G = Stunde ohne
// führende Null). Der Cast (int) macht aus dem Text eine echte Zahl.
$stunde = (int) date('G');

// Passend zur Tageszeit eine Begrüßung wählen. match vergleicht die
// Bedingungen von oben nach unten und nimmt den ersten Treffer.
$begruessung = match (true) {
    $stunde < 6  => 'Gute Nacht',
    $stunde < 11 => 'Guten Morgen',
    $stunde < 18 => 'Guten Tag',
    default      => 'Guten Abend',
};

// Uhrzeit im Format HH:MM für die Anzeige.
$uhrzeit = date('H:i');

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Begrüßung</title>
</head>
<body>
    <h1><?= $begruessung ?>!</h1>
    <p>Es ist <?= $uhrzeit ?> Uhr.</p>
</body>
</html>
