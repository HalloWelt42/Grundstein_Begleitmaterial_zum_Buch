<?php

declare(strict_types=1);

namespace App\Tests;

use App\Kunde;
use App\KundenRepository;
use App\PdoKundenRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 50: Integrationstests
 *
 * Das echte PdoKundenRepository gegen eine echte Datenbank - hier SQLite
 * im Arbeitsspeicher. Kein Fake, kein Double: Jeder Test läuft durch
 * echtes SQL und beweist damit das Zusammenspiel von Repository, PDO und
 * Datenbank, das ein Unit-Test mit einem In-Memory-Fake nicht prüfen kann.
 */
#[CoversClass(PdoKundenRepository::class)]
final class KundenRepositoryTest extends TestCase
{
    private KundenRepository $repository;

    protected function setUp(): void
    {
        // Frische In-Memory-Datenbank pro Test. sqlite::memory: erzeugt bei
        // jeder neuen Verbindung eine leere Datenbank - ideal für Isolation.
        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // SQLite prüft Fremdschlüssel nur, wenn dieses Pragma pro Verbindung
        // gesetzt ist - sonst gingen Verstöße stillschweigend durch.
        $pdo->exec('PRAGMA foreign_keys = ON;');

        // Das echte Schema anlegen, gegen das der Code arbeitet.
        $schema = file_get_contents(dirname(__DIR__) . '/src/schema.sql');
        $pdo->exec($schema);

        $this->repository = new PdoKundenRepository($pdo);
    }

    #[Test]
    public function speichert_einen_neuen_kunden_und_vergibt_eine_id(): void
    {
        // Der neue Kunde hat noch keine id.
        $neuer = Kunde::neu('Ada', 'ada@example.org', 12900);
        self::assertNull($neuer->id);

        $gespeichert = $this->repository->save($neuer);

        // Nach dem Speichern hat die Datenbank eine echte id vergeben.
        self::assertSame(1, $gespeichert->id);
    }

    #[Test]
    public function findet_einen_gespeicherten_kunden_unveraendert_wieder(): void
    {
        $gespeichert = $this->repository->save(
            Kunde::neu('Björn', 'bjoern@example.org', 4500)
        );

        // Frisch aus der Datenbank laden - nicht das Objekt von oben.
        $geladen = $this->repository->find($gespeichert->id ?? 0);

        self::assertNotNull($geladen);
        self::assertSame('Björn', $geladen->name);
        self::assertSame('bjoern@example.org', $geladen->email);
        self::assertSame(4500, $geladen->umsatzCent);
    }

    #[Test]
    public function liefert_null_fuer_einen_unbekannten_kunden(): void
    {
        // Kein Treffer muss ein sauberes null werden - nicht false, das
        // PDO von sich aus liefert. Genau das leistet find().
        self::assertNull($this->repository->find(999));
    }

    #[Test]
    public function jeder_test_beginnt_mit_einer_leeren_datenbank(): void
    {
        // Obwohl andere Tests längst Kunden gespeichert haben, ist diese
        // Datenbank frisch und leer - der Beweis für die Isolation durch
        // setUp(). Der erste Kunde bekommt darum wieder die id 1.
        self::assertCount(0, $this->repository->findAll());

        $kunde = $this->repository->save(Kunde::neu('Cem', 'cem@example.org'));
        self::assertSame(1, $kunde->id);
    }

    #[Test]
    public function die_datenbank_erzwingt_eine_eindeutige_email(): void
    {
        $this->repository->save(Kunde::neu('Ada', 'ada@example.org'));

        // Das UNIQUE-Constraint des Schemas verbietet die zweite Adresse.
        // Genau das kann ein In-Memory-Fake NICHT prüfen - er kennt kein
        // Schema. Deshalb braucht es den Test gegen die echte Datenbank.
        $this->expectException(PDOException::class);
        $this->repository->save(Kunde::neu('Ada Zwei', 'ada@example.org'));
    }

    #[Test]
    public function findall_liefert_alle_kunden_nach_namen_sortiert(): void
    {
        $this->legeBeispielkundenAn();

        $namen = array_map(
            static fn (Kunde $kunde): string => $kunde->name,
            $this->repository->findAll(),
        );

        self::assertSame(['Ada', 'Björn', 'Cem'], $namen);
    }

    /**
     * Fixtures: ein bekannter Ausgangsbestand, den mehrere Tests teilen.
     * Bewusst in ungeordneter Reihenfolge angelegt, damit die Sortierung
     * von findAll() wirklich etwas zu tun bekommt.
     */
    private function legeBeispielkundenAn(): void
    {
        $this->repository->save(Kunde::neu('Cem', 'cem@example.org', 30050));
        $this->repository->save(Kunde::neu('Ada', 'ada@example.org', 12900));
        $this->repository->save(Kunde::neu('Björn', 'bjoern@example.org', 4500));
    }
}
