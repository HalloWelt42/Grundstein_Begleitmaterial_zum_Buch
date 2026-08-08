<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 3: Startseite mit dynamischer Liste.
 *
 * Der eingebaute Webserver liefert diese Datei automatisch aus, wenn im
 * Verzeichnis eine index.php liegt und keine andere Datei angefragt wird.
 * Auch hier gilt die Trennung: erst die Logik im Kopf, dann die
 * Darstellung im Rumpf. Die Liste könnte später aus einer Datenbank
 * kommen - der HTML-Rumpf müsste sich dafür nicht ändern.
 */

// --- Logik --------------------------------------------------------------

// Aktuelle Stunde (0 bis 23) und dazu passende Begrüßung.
$stunde = (int) date('G');

$begruessung = match (true) {
    $stunde < 6  => 'Gute Nacht',
    $stunde < 11 => 'Guten Morgen',
    $stunde < 18 => 'Guten Tag',
    default      => 'Guten Abend',
};

// Eine kleine Liste, die der Rumpf gleich durchläuft.
$themen = [
    'Der eingebaute Webserver',
    'HTML aus PHP erzeugen',
    'Logik und Darstellung trennen',
];

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Grundstein - Startseite</title>
</head>
<body>
    <h1><?= $begruessung ?>!</h1>
    <p>Heute lernst du:</p>
    <ul>
<?php foreach ($themen as $thema): ?>
        <li><?= $thema ?></li>
<?php endforeach; ?>
    </ul>
</body>
</html>
