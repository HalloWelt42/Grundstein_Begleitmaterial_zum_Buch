<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang B: Datum und Zeit
 *
 * DateTimeImmutable erzeugen, formatieren, verschieben und die Differenz
 * zweier Zeitpunkte berechnen - mit fester Zeitzone, damit der Lauf
 * reproduzierbar ist. Ausgaben aus echtem 8.4-Lauf.
 */

$zone = new DateTimeZone('Europe/Berlin');

// --- Erzeugen und formatieren ------------------------------------------

$start = new DateTimeImmutable('2026-08-10 09:30:00', $zone);
echo $start->format('d.m.Y H:i'), "\n";
echo $start->format('l'), "\n";               // Wochentag (englisch)

// --- Verschieben (liefert ein NEUES Objekt) ----------------------------

$spaeter = $start->modify('+2 days +3 hours');
echo $spaeter->format('d.m.Y H:i'), "\n";
echo $start->format('d.m.Y H:i'), "\n";       // Original unverändert

// --- Differenz zweier Zeitpunkte ---------------------------------------

$ende = new DateTimeImmutable('2026-12-24 18:00:00', $zone);
$diff = $start->diff($ende);
echo $diff->days . " Tage bis Heiligabend\n";
echo $diff->format('%m Monate, %d Tage'), "\n";
