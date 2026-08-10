<?php

declare(strict_types=1);

// Mit strict_types wandelt PHP an Funktionsgrenzen nichts still um: Ein Wert,
// der nicht zum deklarierten Typ passt, löst einen TypeError aus.

function flaeche(int $breite, int $hoehe): int
{
    return $breite * $hoehe;
}

// Fehler: die Breite kommt als Zeichenkette aus einem Formular.
$ausFormular = '7';

try {
    echo flaeche($ausFormular, 3) . "\n";
} catch (TypeError $fehler) {
    echo 'TypeError: ' . $fehler->getMessage() . "\n";
}

// Korrektur: den Wert bewusst umwandeln, bevor er die Grenze überquert.
echo flaeche((int) $ausFormular, 3) . "\n";
