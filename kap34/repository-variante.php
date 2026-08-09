<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 34: Ausblick ORM
 *
 * Die Repository-Variante. Derselbe kleine Anwendungsfall - einen
 * Buchbestand pflegen und abfragen - so, wie wir ihn mit den Bausteinen
 * aus Teil V von Hand bauen: ein schlichtes Datenobjekt (DTO), ein
 * Repository, das den Datenzugriff hinter Methoden versteckt, und
 * Prepared Statements auf einer PDO-Verbindung.
 *
 * Diese Datei ist vollständig lauffähig. Sie braucht keinen Server und
 * keine Installation, weil sie SQLite im Arbeitsspeicher nutzt; der
 * Treiber pdo_sqlite steckt im PHP-Abbild bereits drin.
 *
 *   docker run --rm -v "$PWD":/app -w /app php:8.4-cli \
 *       php kap34/repository-variante.php
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/**
 * Das Datenobjekt (DTO). Es trägt genau die Werte einer Buchzeile und
 * sonst nichts - kein SQL, keine Datenbankkenntnis. Als readonly-Objekt
 * ist es nach dem Erzeugen unveränderlich.
 */
final class Buch
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $titel,
        public readonly int $jahr,
    ) {}
}

/**
 * Das Repository. Nur diese Klasse kennt SQL und PDO. Der Rest des
 * Programms bittet sie um Bücher und bekommt fertige Buch-Objekte
 * zurück, ohne je eine Abfrage zu sehen.
 */
final class BuchRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    /**
     * Legt ein Buch an und liefert es mit vergebener id zurück.
     */
    public function speichern(Buch $buch): Buch
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO buch (titel, jahr) VALUES (:titel, :jahr)'
        );
        $stmt->execute([
            'titel' => $buch->titel,
            'jahr'  => $buch->jahr,
        ]);

        // Die frisch vergebene id aus der Datenbank übernehmen.
        $id = (int) $this->pdo->lastInsertId();

        return new Buch($id, $buch->titel, $buch->jahr);
    }

    /**
     * Sucht ein Buch anhand seiner id; null, wenn es keines gibt.
     */
    public function finde(int $id): ?Buch
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, titel, jahr FROM buch WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        $zeile = $stmt->fetch();
        if ($zeile === false) {
            return null;
        }

        return $this->zuBuch($zeile);
    }

    /**
     * Liefert alle Bücher ab einem Jahr, nach Jahr sortiert.
     *
     * @return list<Buch>
     */
    public function findeAbJahr(int $jahr): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, titel, jahr FROM buch WHERE jahr >= :jahr ORDER BY jahr'
        );
        $stmt->execute(['jahr' => $jahr]);

        // Jede rohe Zeile wird in ein sauberes Buch-Objekt übersetzt.
        return array_map(
            fn (array $zeile): Buch => $this->zuBuch($zeile),
            $stmt->fetchAll(),
        );
    }

    /**
     * Die eine Stelle, an der aus einer Ergebniszeile ein Objekt wird.
     *
     * @param array<string, mixed> $zeile
     */
    private function zuBuch(array $zeile): Buch
    {
        return new Buch(
            (int) $zeile['id'],
            (string) $zeile['titel'],
            (int) $zeile['jahr'],
        );
    }
}

// --- Verdrahtung: Verbindung und Schema -----------------------------

$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

$pdo->exec(
    'CREATE TABLE buch (
        id    INTEGER PRIMARY KEY,
        titel TEXT    NOT NULL,
        jahr  INTEGER NOT NULL
    )'
);

// --- Anwendungscode: kennt nur das Repository, nie SQL ---------------

$repository = new BuchRepository($pdo);

// Drei Bücher anlegen. Der Anwendungscode denkt in Objekten.
$gespeichert = [];
foreach ([
    new Buch(null, 'Grundstein', 2026),
    new Buch(null, 'Fundament', 2019),
    new Buch(null, 'Aufbau', 2023),
] as $neu) {
    $gespeichert[] = $repository->speichern($neu);
}

echo 'Angelegt: ' . count($gespeichert) . ' Bücher.' . PHP_EOL;

// Ein einzelnes Buch über seine id holen.
$erstes = $gespeichert[0];
$geladen = $repository->finde($erstes->id ?? 0);
if ($geladen !== null) {
    echo "Geladen: {$geladen->titel} ({$geladen->jahr})" . PHP_EOL;
}

// Alle Bücher ab 2020 - das Repository liefert fertige Objekte.
echo 'Bücher ab 2020:' . PHP_EOL;
foreach ($repository->findeAbJahr(2020) as $buch) {
    echo "  {$buch->jahr}: {$buch->titel}" . PHP_EOL;
}
