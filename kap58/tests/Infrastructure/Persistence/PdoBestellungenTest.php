<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence;

use App\Domain\EmailAdresse;
use App\Domain\Geldbetrag;
use App\Domain\Bestellung;
use App\Infrastructure\Persistence\PdoBestellungen;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Der Integrationstest für den PDO-Adapter (Kapitel 50, 55). Er betreibt die
 * echte Klasse gegen eine echte Datenbank - SQLite im Arbeitsspeicher, ohne
 * Server. setUp() legt für jeden Test eine frische, leere Datenbank an; so ist
 * die Isolation geschenkt. Nur dieser Test kann beweisen, dass das SQL, die
 * id-Vergabe und das Mapping zurück ins Domänen-Objekt wirklich stimmen.
 */
final class PdoBestellungenTest extends TestCase
{
    private PdoBestellungen $bestellungen;

    protected function setUp(): void
    {
        // Frische In-Memory-Datenbank pro Test - vollständige Isolation.
        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // Dasselbe Schema, gegen das der Adapter in Produktion arbeitet.
        $schema = (string) file_get_contents(
            dirname(__DIR__, 3) . '/src/Infrastructure/Persistence/schema.sql'
        );
        $pdo->exec($schema);

        $this->bestellungen = new PdoBestellungen($pdo);
    }

    #[Test]
    public function speichert_und_liest_eine_bestellung_unveraendert_wieder(): void
    {
        $gespeichert = $this->bestellungen->save(Bestellung::neu(
            new EmailAdresse('ada@example.org'),
            Geldbetrag::ausEuro(49.90),
        ));

        // Die Datenbank vergibt die id.
        self::assertSame(1, $gespeichert->id);

        // Frisch aus der Datenbank laden - nicht das Objekt von oben.
        $geladen = $this->bestellungen->find(1);
        self::assertNotNull($geladen);
        self::assertSame('ada@example.org', $geladen->kunde->wert);
        self::assertSame(4990, $geladen->betrag->cent);
        self::assertFalse($geladen->istBezahlt());
    }

    #[Test]
    public function ein_update_legt_keinen_zweiten_datensatz_an(): void
    {
        $offen   = $this->bestellungen->save(Bestellung::neu(
            new EmailAdresse('ada@example.org'),
            Geldbetrag::ausEuro(49.90),
        ));
        $bezahlt = $offen->bezahle();

        $this->bestellungen->save($bezahlt);

        // Es gibt weiterhin genau einen Datensatz, jetzt mit neuem Status.
        self::assertCount(1, $this->bestellungen->alle());
        $geladen = $this->bestellungen->find($offen->id ?? 0);
        self::assertNotNull($geladen);
        self::assertTrue($geladen->istBezahlt());
    }

    #[Test]
    public function liefert_null_fuer_eine_unbekannte_id(): void
    {
        // Kein Treffer wird ein sauberes null - nicht das false von PDO.
        self::assertNull($this->bestellungen->find(999));
    }
}
