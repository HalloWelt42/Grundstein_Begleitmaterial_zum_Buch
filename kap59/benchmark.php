<?php

declare(strict_types=1);

/*
 * Ein kleines Messwerkzeug und ein ehrlicher Vergleich zweier Lösungen
 * derselben Aufgabe: Bestellungen mit ihren Kunden verknüpfen - einmal
 * naiv (eine lineare Suche je Bestellung, das In-Memory-Gegenstück zum
 * N+1-Problem aus Kapitel 32) und einmal mit einem einmal aufgebauten Index.
 */

/**
 * Liefert den Median einer Liste von Messwerten.
 *
 * Der Median ist bei Zeitmessungen ehrlicher als der Mittelwert: Ein
 * einzelner Ausreißer nach oben (etwa durch die Speicherbereinigung) zieht
 * ihn nicht mit.
 *
 * @param list<float> $werte
 */
function median(array $werte): float
{
    sort($werte);
    $anzahl = count($werte);
    $mitte = intdiv($anzahl, 2);

    // Ungerade Anzahl: der mittlere Wert. Gerade: Mittel der beiden mittleren.
    return $anzahl % 2 === 1
        ? $werte[$mitte]
        : ($werte[$mitte - 1] + $werte[$mitte]) / 2;
}

/**
 * Misst die Laufzeit von $code als Median mehrerer Durchläufe (in Millisekunden).
 *
 * hrtime(true) liefert einen monotonen Zeitstempel in Nanosekunden - die
 * richtige Wahl für Zeitmessungen, weil er stetig steigt und nicht an der
 * Wanduhr hängt. Ein paar Aufwärmläufe vorab sorgen dafür, dass einmalige
 * Anlaufkosten das erste echte Ergebnis nicht verfälschen.
 */
function messen(callable $code, int $proben = 15, int $aufwaermen = 3): float
{
    // Aufwärmen: Ergebnis verwerfen, nur die Anlaufkosten "verbrauchen".
    for ($i = 0; $i < $aufwaermen; $i++) {
        $code();
    }

    $zeiten = [];
    for ($i = 0; $i < $proben; $i++) {
        $start = hrtime(true);
        $code();
        $zeiten[] = (hrtime(true) - $start) / 1_000_000; // Nanosekunden -> Millisekunden
    }

    return median($zeiten);
}

/**
 * Naiv: für jede Bestellung die ganze Kundenliste linear durchsuchen.
 * Bei n Bestellungen und n Kunden sind das n mal n Vergleiche.
 *
 * @param list<array{kundeId: int, betrag: int}> $bestellungen
 * @param list<array{id: int, name: string}>     $kunden
 * @return list<string>
 */
function verknuepfeNaiv(array $bestellungen, array $kunden): array
{
    $zeilen = [];
    foreach ($bestellungen as $bestellung) {
        $name = 'unbekannt';
        foreach ($kunden as $kunde) {        // innere Schleife: der teure Teil
            if ($kunde['id'] === $bestellung['kundeId']) {
                $name = $kunde['name'];
                break;
            }
        }
        $zeilen[] = "{$name}: {$bestellung['betrag']}";
    }

    return $zeilen;
}

/**
 * Mit Index: die Kunden einmal nach id in eine Map legen, dann in nahezu
 * konstanter Zeit nachschlagen. Aus n mal n wird n plus n.
 *
 * @param list<array{kundeId: int, betrag: int}> $bestellungen
 * @param list<array{id: int, name: string}>     $kunden
 * @return list<string>
 */
function verknuepfeMitIndex(array $bestellungen, array $kunden): array
{
    $nameNachId = [];
    foreach ($kunden as $kunde) {            // einmalig: den Index aufbauen
        $nameNachId[$kunde['id']] = $kunde['name'];
    }

    $zeilen = [];
    foreach ($bestellungen as $bestellung) {
        $name = $nameNachId[$bestellung['kundeId']] ?? 'unbekannt';
        $zeilen[] = "{$name}: {$bestellung['betrag']}";
    }

    return $zeilen;
}

// --- Testdaten aufbauen ---------------------------------------------------
$anzahl = 2000;

$kunden = [];
for ($i = 1; $i <= $anzahl; $i++) {
    $kunden[] = ['id' => $i, 'name' => "Kunde {$i}"];
}

$bestellungen = [];
for ($i = 0; $i < $anzahl; $i++) {
    $bestellungen[] = ['kundeId' => random_int(1, $anzahl), 'betrag' => random_int(100, 9900)];
}

// --- Sicherstellen, dass beide Wege dasselbe Ergebnis liefern -------------
if (verknuepfeNaiv($bestellungen, $kunden) !== verknuepfeMitIndex($bestellungen, $kunden)) {
    fwrite(STDERR, 'Die beiden Lösungen liefern Unterschiedliches - Messung sinnlos.' . PHP_EOL);
    exit(1);
}

// --- Messen ---------------------------------------------------------------
$naiv  = messen(fn(): array => verknuepfeNaiv($bestellungen, $kunden));
$index = messen(fn(): array => verknuepfeMitIndex($bestellungen, $kunden));

printf('Datensätze:      %d Bestellungen x %d Kunden%s', $anzahl, $anzahl, PHP_EOL);
printf('Naiv (N+1):      %8.3f ms (Median)%s', $naiv, PHP_EOL);
printf('Mit Index:       %8.3f ms (Median)%s', $index, PHP_EOL);
printf('Beschleunigung:  rund %d-fach (Größenordnung)%s', (int) round($naiv / $index), PHP_EOL);
