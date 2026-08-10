<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 35: Eine HTTP-Anfrage beantworten.
 *
 * Dieses Skript liest zwei Angaben aus der eingehenden Anfrage - die
 * verwendete Methode und einen Namen aus der Abfragezeichenkette - und
 * baut daraus eine kleine HTML-Antwort. Alles, was aus der Anfrage
 * stammt und ins HTML wandert, wird vorher mit htmlspecialchars
 * entschärft. So kann eine bösartige Eingabe kein fremdes Markup in die
 * Seite schmuggeln.
 */

// Die vom Server erkannte HTTP-Methode, etwa GET oder POST. Fehlt sie
// (beim Aufruf über die Kommandozeile), nehmen wir GET an.
$methode = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Den Namen aus der Abfragezeichenkette lesen (?name=...). Ist keiner
// angegeben, greift der Standardwert.
$name = $_GET['name'] ?? 'Welt';

// Vor dem Einbau ins HTML entschärfen: aus < wird &lt;, aus " wird
// &quot; und so weiter. ENT_QUOTES wandelt einfache UND doppelte
// Anführungszeichen um; ENT_SUBSTITUTE ersetzt ungültige Zeichen durch
// ein Ersatzzeichen, statt eine leere Zeichenkette zu liefern. Genau
// diese Kombination ist seit PHP 8.1 ohnehin der Vorgabewert - wir
// schreiben sie hier bewusst aus, damit die sichere Absicht sichtbar ist.
$sicherName = htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE);
$sicherMethode = htmlspecialchars($methode, ENT_QUOTES | ENT_SUBSTITUTE);

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Hallo HTTP</title>
</head>
<body>
    <h1>Hallo, <?= $sicherName ?>!</h1>
    <p>Diese Antwort kam auf eine <?= $sicherMethode ?>-Anfrage.</p>
</body>
</html>
