<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 32: Datenzugriff kapseln
 *
 * Einstiegspunkt. Er baut die PDO-Verbindung (SQLite im Speicher) und
 * das Schema auf, füllt das Repository und zeigt dann, wie die Anwendung
 * ausschließlich über den Vertrag KundenRepository mit den Daten spricht -
 * ohne ein einziges SQL an dieser Stelle.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

require __DIR__ . '/autoload.php';

use App\InMemoryKundenRepository;
use App\Kunde;
use App\KundenRepository;
use App\PdoKundenRepository;

/**
 * Baut eine frische SQLite-Verbindung im Speicher mit den drei
 * empfohlenen Optionen aus Kapitel 31 und legt die Tabelle kunde an.
 */
function verbindungMitSchema(): PDO
{
    $pdo = new PDO('sqlite::memory:', null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    $pdo->exec(
        'CREATE TABLE kunde (
            id          INTEGER PRIMARY KEY,
            name        TEXT    NOT NULL,
            email       TEXT    NOT NULL UNIQUE,
            umsatz_cent INTEGER NOT NULL DEFAULT 0
        )'
    );

    return $pdo;
}

/*
 * Eine kleine Anwendungsfunktion. Entscheidend ist ihr Parametertyp:
 * Sie verlangt das Interface KundenRepository, nicht die konkrete
 * PDO-Klasse. Dadurch weiß sie nichts von SQL und funktioniert mit jedem
 * Repository, das den Vertrag erfüllt.
 */
function umsatzBericht(KundenRepository $kunden): string
{
    $zeilen = [];
    $summe  = 0;

    foreach ($kunden->findAll() as $kunde) {
        // mb_str_pad zählt Zeichen, nicht Bytes - so bleiben auch Namen
        // mit Umlauten sauber ausgerichtet.
        $zeilen[] = '  ' . mb_str_pad($kunde->name, 8) . $kunde->umsatzEuro();
        $summe   += $kunde->umsatzCent;
    }

    $zeilen[] = '  ' . mb_str_pad('Summe', 8) . sprintf('%.2f EUR', $summe / 100);

    return implode(PHP_EOL, $zeilen);
}

// --- 1. Das PDO-Repository befüllen ---------------------------------
$repository = new PdoKundenRepository(verbindungMitSchema());

// save() nimmt einen Kunden OHNE id und gibt ihn MIT id zurück.
$anja = $repository->save(Kunde::neu('Anja', 'anja@example.org', 12900));
echo "Angelegt: {$anja->name} hat jetzt id {$anja->id}." . PHP_EOL;

$repository->save(Kunde::neu('Björn', 'bjoern@example.org', 4500));
$repository->save(Kunde::neu('Cem',   'cem@example.org',    30050));

// --- 2. Einen einzelnen Kunden finden -------------------------------
$gefunden = $repository->find($anja->id);
echo 'Gefunden: ' . ($gefunden?->name ?? '-') . ' (' . ($gefunden?->email ?? '-') . ')' . PHP_EOL;

$fehlt = $repository->find(999);
echo 'Kunde 999: ' . ($fehlt === null ? 'nicht vorhanden' : $fehlt->name) . PHP_EOL;

// --- 3. Bericht über den Vertrag, nicht über SQL --------------------
echo 'Umsatz je Kunde (PDO):' . PHP_EOL;
echo umsatzBericht($repository) . PHP_EOL;

// --- 4. Löschen und Ergebnis prüfen ---------------------------------
$repository->delete($anja->id);
echo 'Nach dem Löschen von Anja noch ' . count($repository->findAll()) . ' Kunden.' . PHP_EOL;

// --- 5. Dieselbe Berichtsfunktion, anderes Repository ---------------
// Kein SQL, keine Datenbank - und doch funktioniert alles unverändert,
// weil beide denselben Vertrag erfüllen.
$speicher = new InMemoryKundenRepository();
$speicher->save(Kunde::neu('Dörte', 'doerte@example.org', 9900));
$speicher->save(Kunde::neu('Emil',  'emil@example.org',  15000));

echo 'Umsatz je Kunde (im Speicher):' . PHP_EOL;
echo umsatzBericht($speicher) . PHP_EOL;
