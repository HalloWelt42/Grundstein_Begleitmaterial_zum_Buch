<?php

declare(strict_types=1);

// match ist ein Ausdruck: er vergleicht strikt (===) und liefert einen Wert.
$code = 404;
$text = match ($code) {
    200, 204 => 'OK',
    301, 302 => 'Weiterleitung',
    404      => 'Nicht gefunden',
    default  => 'Unbekannt',
};
echo "Status {$code}: {$text}\n";

// match (true) ersetzt eine lange if-elseif-Kette.
$temperatur = 22;
$stufe = match (true) {
    $temperatur >= 30 => 'heiß',
    $temperatur >= 20 => 'warm',
    default           => 'kühl',
};
echo "Wetter: {$stufe}\n";

// break mit Ebene verlässt mehrere Schleifen auf einmal.
foreach (['a', 'b'] as $zeile) {
    foreach ([1, 2, 3] as $spalte) {
        if ($spalte === 2) {
            break 2;   // beide Schleifen verlassen
        }
        echo "{$zeile}{$spalte} ";
    }
}
echo "\n";
