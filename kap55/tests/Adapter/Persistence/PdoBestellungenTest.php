<?php

declare(strict_types=1);

namespace App\Tests\Adapter\Persistence;

use App\Adapter\Persistence\PdoBestellungen;
use App\Domain\Bestellung;
use App\Domain\Geld;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Der Integrationstest des getriebenen Adapters gegen eine echte Datenbank -
 * SQLite im Arbeitsspeicher, genau wie in Kapitel 50. Er beweist, dass
 * PdoBestellungen den Port mit echtem SQL korrekt erfüllt.
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
            dirname(__DIR__, 3) . '/src/Adapter/Persistence/schema.sql'
        );
        $pdo->exec($schema);

        $this->bestellungen = new PdoBestellungen($pdo);
    }

    #[Test]
    public function speichert_und_liest_eine_bestellung_unveraendert_wieder(): void
    {
        $gespeichert = $this->bestellungen->save(
            Bestellung::neu('Björn', Geld::ausEuro(250.0))
        );
        self::assertSame(1, $gespeichert->id);   // die Datenbank vergibt die id

        // Frisch aus der Datenbank laden - nicht das Objekt von oben.
        $geladen = $this->bestellungen->find(1);
        self::assertNotNull($geladen);
        self::assertSame(25000, $geladen->betrag->cent);
        self::assertTrue($geladen->istOffen());
    }

    #[Test]
    public function liefert_null_fuer_eine_unbekannte_id(): void
    {
        self::assertNull($this->bestellungen->find(999));
    }

    #[Test]
    public function listet_nur_die_offenen_bestellungen(): void
    {
        $offen  = $this->bestellungen->save(Bestellung::neu('Ada', Geld::ausEuro(49.90)));
        $this->bestellungen->save(Bestellung::neu('Cem', Geld::ausEuro(19.90)));

        // Die erste Bestellung bezahlen und den bezahlten Zustand speichern.
        $this->bestellungen->save(
            ($this->bestellungen->find($offen->id ?? 0))->alsBezahltMarkiert()
        );

        // Es bleibt genau eine offene Bestellung übrig.
        self::assertCount(1, $this->bestellungen->alleOffenen());
    }
}
