<?php

declare(strict_types=1);

// Zeigt, wie man Eingaben direkt aus der Anfrage geprüft liest.
// filter_input holt einen Wert aus einer Quelle (hier GET) und prüft
// ihn in einem Schritt. Schlägt die Prüfung fehl, kommt false zurück;
// fehlt der Wert ganz, kommt null zurück. Beides fängt der ??-Operator
// mit einem sinnvollen Standardwert ab.

// Ganzzahl 1..120 erwarten - alles andere gilt als ungültig.
$alter = filter_input(INPUT_GET, 'alter', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 120],
]);

// E-Mail-Adresse prüfen. filter_var arbeitet auf einem beliebigen Wert,
// filter_input direkt auf der Quelle - sonst sind beide gleich.
$mail = filter_input(INPUT_GET, 'mail', FILTER_VALIDATE_EMAIL);

// Freitext hat keine Formatprüfung - hier zählt nur, ihn beim Ausgeben
// zu escapen. Ein fehlender Wert wird mit ?? zu einem leeren String.
$rohName = $_GET['name'] ?? '';

?>
    <p>Name: <?= htmlspecialchars($rohName, ENT_QUOTES) ?></p>
    <p>Alter: <?= $alter === false ? 'ungültig' : ($alter ?? 'nicht angegeben') ?></p>
    <p>E-Mail: <?= $mail === false ? 'ungültig' : ($mail === null ? 'nicht angegeben' : htmlspecialchars($mail, ENT_QUOTES)) ?></p>
