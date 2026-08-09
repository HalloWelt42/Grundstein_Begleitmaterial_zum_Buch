<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 31: Datenbankzugriff mit PDO
 *
 * Teil 4: Transaktionen. Mehrere Änderungen, die zusammengehören,
 * werden mit beginTransaction() geklammert. Erst commit() macht sie
 * dauerhaft; tritt zwischendurch ein Fehler auf, macht rollBack() alle
 * Änderungen der Transaktion rückgängig - entweder alles oder nichts.
 *
 * Beispiel: Geld von einem Konto auf ein anderes umbuchen. Abbuchen und
 * Gutschreiben dürfen nur gemeinsam gelten.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

$optionen = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO('sqlite::memory:', null, null, $optionen);

$pdo->exec(
    'CREATE TABLE konto (
        id          INTEGER PRIMARY KEY,
        inhaber     TEXT    NOT NULL,
        stand_cent  INTEGER NOT NULL
    )'
);
$pdo->exec(
    "INSERT INTO konto (inhaber, stand_cent) VALUES
        ('Anja', 5000),
        ('Björn', 1000)"
);

/**
 * Bucht einen Betrag (in Cent) von einem Konto auf ein anderes um.
 * Reicht die Deckung nicht, wird die ganze Transaktion zurückgerollt,
 * damit kein halber Vorgang stehen bleibt.
 *
 * @throws RuntimeException wenn das Quellkonto nicht genug Deckung hat
 */
function umbuchen(PDO $pdo, int $vonId, int $nachId, int $betragCent): void
{
    $pdo->beginTransaction();

    try {
        $abbuchen = $pdo->prepare(
            'UPDATE konto SET stand_cent = stand_cent - :betrag WHERE id = :id'
        );
        $abbuchen->execute(['betrag' => $betragCent, 'id' => $vonId]);

        // Stand des Quellkontos prüfen - darf nicht negativ werden.
        $frage = $pdo->prepare('SELECT stand_cent FROM konto WHERE id = :id');
        $frage->execute(['id' => $vonId]);
        $stand = (int) $frage->fetchColumn();

        if ($stand < 0) {
            throw new RuntimeException('Deckung reicht nicht.');
        }

        $gutschreiben = $pdo->prepare(
            'UPDATE konto SET stand_cent = stand_cent + :betrag WHERE id = :id'
        );
        $gutschreiben->execute(['betrag' => $betragCent, 'id' => $nachId]);

        // Alles hat geklappt - jetzt dauerhaft festschreiben.
        $pdo->commit();
    } catch (Throwable $fehler) {
        // Irgendetwas ging schief: alle Änderungen der Transaktion
        // verwerfen und den Fehler weiterreichen.
        $pdo->rollBack();
        throw $fehler;
    }
}

/**
 * Gibt den Stand beider Konten aus - zum Nachvollziehen der Wirkung.
 */
function staendeZeigen(PDO $pdo, string $titel): void
{
    echo $titel . PHP_EOL;
    foreach ($pdo->query('SELECT inhaber, stand_cent FROM konto ORDER BY id') as $zeile) {
        printf('  %8.2f Euro - %s' . PHP_EOL, $zeile['stand_cent'] / 100, $zeile['inhaber']);
    }
}

staendeZeigen($pdo, 'Vorher:');

// Erste Buchung: 20,00 Euro von Anja an Björn - passt.
umbuchen($pdo, 1, 2, 2000);
staendeZeigen($pdo, 'Nach gültiger Buchung (20,00 Euro):');

// Zweite Buchung: 90,00 Euro von Anja - zu viel. Die Transaktion wird
// zurückgerollt, die Stände bleiben unverändert.
try {
    umbuchen($pdo, 1, 2, 9000);
} catch (RuntimeException $fehler) {
    echo 'Buchung abgelehnt: ' . $fehler->getMessage() . PHP_EOL;
}

staendeZeigen($pdo, 'Nach abgelehnter Buchung (unverändert):');
