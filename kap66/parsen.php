<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 66: Datum, Zeit und Zeitzonen
 *
 * Parsen mit createFromFormat und ehrliche Fehlerbehandlung. Zwei Fallen
 * lauern hier: fehlende Bestandteile werden aus der AKTUELLEN Zeit
 * aufgefüllt (das "!" verhindert das), und ungültige Daten werden still
 * überrollt (nur getLastErrors deckt das auf).
 */

/**
 * Parst streng: liefert nur bei fehlerfreiem, eindeutigem Ergebnis ein
 * Objekt und wirft sonst. Das führende "!" im Muster setzt alle nicht
 * genannten Felder auf den Beginn der Unix-Zeit zurück, statt sie aus der
 * aktuellen Uhrzeit zu raten.
 */
function parseStrikt(string $muster, string $eingabe, DateTimeZone $zone): DateTimeImmutable
{
    $ergebnis = DateTimeImmutable::createFromFormat('!' . $muster, $eingabe, $zone);
    $fehler = DateTimeImmutable::getLastErrors();

    // false = gar nicht parsebar; getLastErrors meldet zusätzlich
    // Warnungen (etwa ein überrolltes Datum wie den 31. Februar).
    $hatMeldung = $fehler !== false
        && ($fehler['warning_count'] > 0 || $fehler['error_count'] > 0);

    if ($ergebnis === false || $hatMeldung) {
        throw new InvalidArgumentException("Ungültiges Datum: '{$eingabe}'");
    }

    return $ergebnis;
}

$utc = new DateTimeZone('UTC');

echo '--- Gültige Eingabe ---' . PHP_EOL;
$gut = parseStrikt('d.m.Y H:i', '28.03.2026 09:30', $utc);
echo $gut->format('Y-m-d H:i:s') . PHP_EOL; // Sekunden dank "!" sauber 00

echo PHP_EOL . '--- Überrolltes Datum (31. Februar) ---' . PHP_EOL;
try {
    parseStrikt('d.m.Y', '31.02.2026', $utc);
} catch (InvalidArgumentException $fehler) {
    echo 'Abgefangen: ' . $fehler->getMessage() . PHP_EOL;
}

echo PHP_EOL . '--- Unparsebare Eingabe ---' . PHP_EOL;
try {
    parseStrikt('d.m.Y', 'kein datum', $utc);
} catch (InvalidArgumentException $fehler) {
    echo 'Abgefangen: ' . $fehler->getMessage() . PHP_EOL;
}
