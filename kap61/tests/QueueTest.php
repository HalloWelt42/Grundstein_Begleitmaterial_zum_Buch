<?php

declare(strict_types=1);

namespace App\Tests;

use App\Datenbank;
use App\Queue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QueueTest extends TestCase
{
    private Queue $queue;

    protected function setUp(): void
    {
        // Frische Speicherdatenbank für jeden Test - schnell und isoliert.
        $this->queue = new Queue(Datenbank::oeffne(':memory:'));
        $this->queue->migriere();
    }

    #[Test]
    public function lege_zaehlt_den_auftrag_als_offen(): void
    {
        $id = $this->queue->lege('email', ['an' => 'ada@example.org']);

        self::assertGreaterThan(0, $id);
        self::assertSame(1, $this->queue->zaehle('offen'));
    }

    #[Test]
    public function reserviere_liefert_den_aeltesten_auftrag_zuerst(): void
    {
        $this->queue->lege('email', ['an' => 'erste@example.org']);
        $this->queue->lege('bild', ['datei' => 'foto.jpg']);

        $auftrag = $this->queue->reserviere();

        self::assertNotNull($auftrag);
        self::assertSame('email', $auftrag->typ);
        self::assertSame('erste@example.org', $auftrag->daten['an']);
    }

    #[Test]
    public function reserviere_erhoeht_den_versuchszaehler(): void
    {
        $this->queue->lege('email', ['an' => 'ada@example.org']);

        $auftrag = $this->queue->reserviere();

        self::assertNotNull($auftrag);
        self::assertSame(1, $auftrag->versuch);
        self::assertSame(1, $this->queue->zaehle('in_arbeit'));
    }

    #[Test]
    public function reserviere_gibt_denselben_auftrag_nicht_zweimal_heraus(): void
    {
        // Nur ein Auftrag liegt bereit. Zwei Aufrufe stehen für zwei Worker,
        // die gleichzeitig zugreifen. Genau einer darf ihn bekommen.
        $this->queue->lege('email', ['an' => 'ada@example.org']);

        $ersterZugriff  = $this->queue->reserviere();
        $zweiterZugriff = $this->queue->reserviere();

        self::assertNotNull($ersterZugriff);
        self::assertNull($zweiterZugriff); // der zweite geht leer aus
    }

    #[Test]
    public function reserviere_liefert_null_bei_leerer_queue(): void
    {
        self::assertNull($this->queue->reserviere());
    }

    #[Test]
    public function erledige_setzt_den_status_auf_erledigt(): void
    {
        $this->queue->lege('email', ['an' => 'ada@example.org']);
        $auftrag = $this->queue->reserviere();
        self::assertNotNull($auftrag);

        $this->queue->erledige($auftrag->id);

        self::assertSame(1, $this->queue->zaehle('erledigt'));
        self::assertSame(0, $this->queue->zaehle('in_arbeit'));
    }

    #[Test]
    public function meldeFehler_stellt_den_auftrag_zunaechst_zurueck(): void
    {
        $this->queue->lege('bild', ['datei' => 'foto.jpg'], maxVersuche: 3);
        $auftrag = $this->queue->reserviere();
        self::assertNotNull($auftrag);

        $this->queue->meldeFehler($auftrag->id, 'kurzer Ausfall');

        // Es sind noch Versuche übrig, also wieder offen.
        self::assertSame(1, $this->queue->zaehle('offen'));
        self::assertSame(0, $this->queue->zaehle('fehlgeschlagen'));
    }

    #[Test]
    public function meldeFehler_gibt_bei_erschoepften_versuchen_endgueltig_auf(): void
    {
        // Grenze 2: nach dem zweiten Fehlversuch ist der Auftrag tot.
        $this->queue->lege('bild', ['datei' => 'foto.jpg'], maxVersuche: 2);

        $erster = $this->queue->reserviere();
        self::assertNotNull($erster);
        $this->queue->meldeFehler($erster->id, 'Fehler 1');
        self::assertSame(1, $this->queue->zaehle('offen'));

        $zweiter = $this->queue->reserviere();
        self::assertNotNull($zweiter);
        self::assertSame(2, $zweiter->versuch);
        $this->queue->meldeFehler($zweiter->id, 'Fehler 2');

        self::assertSame(0, $this->queue->zaehle('offen'));
        self::assertSame(1, $this->queue->zaehle('fehlgeschlagen'));
    }
}
