<?php

declare(strict_types=1);

/**
 * Template: reine Darstellung, keine Logik.
 *
 * Dieses Template gibt nur aus - es entscheidet nichts und holt keine
 * Daten. Alles, was es braucht, wird ihm von render() als Variable
 * übergeben. Jeder dynamische Wert läuft durch e(), damit keine
 * Nutzereingabe ungefiltert ins HTML gelangt (Schutz vor XSS).
 *
 * Erwartete Variablen:
 * @var string $titel Überschrift der Seite
 * @var list<array{text: string, erledigt: bool}> $aufgaben Liste der Punkte
 */

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title><?= e($titel) ?></title>
</head>
<body>
    <h1><?= e($titel) ?></h1>
<?php if ($aufgaben === []): ?>
    <p>Es sind noch keine Aufgaben eingetragen.</p>
<?php else: ?>
    <ul>
<?php foreach ($aufgaben as $aufgabe): ?>
        <li><?= e($aufgabe['text']) ?><?php if ($aufgabe['erledigt']): ?> (erledigt)<?php endif; ?></li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>
</body>
</html>
