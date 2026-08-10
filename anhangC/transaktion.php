<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang C: PDO-Referenz
 *
 * Beispiel 2: Transaktionen - "alles oder nichts".
 *
 * beginTransaction() eroeffnet die Klammer, commit() schreibt alle
 * Aenderungen endgueltig fest, rollBack() macht sie geschlossen
 * rueckgaengig. Das uebliche Muster steckt die Schritte in ein try und
 * ruft im Fehlerfall rollBack() aus dem catch.
 *
 * Laeuft auf SQLite im Arbeitsspeicher. Alle Ausgaben stammen aus einem
 * echten Lauf mit PHP 8.4.
 */

$optionen = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO('sqlite::memory:', null, null, $optionen);

$pdo->exec(
    'CREATE TABLE konto (
        id         INTEGER PRIMARY KEY,
        inhaber    TEXT    NOT NULL,
        stand_cent INTEGER NOT NULL
    )'
);
$pdo->exec(
    "INSERT INTO konto (inhaber, stand_cent) VALUES
        ('Anja', 5000),
        ('Björn', 1000)"
);

/**
 * Bucht einen Betrag (in Cent) von einem Konto auf ein anderes um.
 * Reicht die Deckung nicht, wird die ganze Transaktion zurueckgerollt.
 *
 * @throws RuntimeException wenn das Quellkonto nicht genug Deckung hat
 */
function umbuchen(PDO $pdo, int $vonId, int $nachId, int $betragCent): void
{
    $pdo->beginTransaction();

    try {
        $ab = $pdo->prepare('UPDATE konto SET stand_cent = stand_cent - :b WHERE id = :id');
        $ab->execute(['b' => $betragCent, 'id' => $vonId]);

        $frage = $pdo->prepare('SELECT stand_cent FROM konto WHERE id = :id');
        $frage->execute(['id' => $vonId]);
        if ((int) $frage->fetchColumn() < 0) {
            throw new RuntimeException('Deckung reicht nicht.');
        }

        $auf = $pdo->prepare('UPDATE konto SET stand_cent = stand_cent + :b WHERE id = :id');
        $auf->execute(['b' => $betragCent, 'id' => $nachId]);

        $pdo->commit();
    } catch (Throwable $fehler) {
        $pdo->rollBack();
        throw $fehler;
    }
}

function staendeZeigen(PDO $pdo, string $titel): void
{
    echo $titel . PHP_EOL;
    foreach ($pdo->query('SELECT inhaber, stand_cent FROM konto ORDER BY id') as $z) {
        printf('  %8.2f Euro - %s' . PHP_EOL, $z['stand_cent'] / 100, $z['inhaber']);
    }
}

staendeZeigen($pdo, 'Vorher:');

// Gueltige Buchung: 20,00 Euro von Anja an Björn.
umbuchen($pdo, 1, 2, 2000);
staendeZeigen($pdo, 'Nach gültiger Buchung (20,00 Euro):');

// Zu grosse Buchung: wird zurueckgerollt, Staende bleiben unveraendert.
try {
    umbuchen($pdo, 1, 2, 9000);
} catch (RuntimeException $fehler) {
    echo 'Buchung abgelehnt: ' . $fehler->getMessage() . PHP_EOL;
}
staendeZeigen($pdo, 'Nach abgelehnter Buchung (unverändert):');
