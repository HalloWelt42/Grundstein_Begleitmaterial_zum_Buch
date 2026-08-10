<?php

declare(strict_types=1);

namespace App\Tests;

use App\Auftrag;
use App\Datenbank;
use App\Queue;
use App\Worker;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class WorkerTest extends TestCase
{
    private Queue $queue;

    /** @var list<string> */
    private array $protokoll = [];

    protected function setUp(): void
    {
        $this->queue = new Queue(Datenbank::oeffne(':memory:'));
        $this->queue->migriere();
        $this->protokoll = [];
    }

    #[Test]
    public function worker_erledigt_alle_offenen_auftraege(): void
    {
        $this->queue->lege('email', ['an' => 'ada@example.org']);
        $this->queue->lege('email', ['an' => 'grace@example.org']);

        $worker = new Worker(
            queue: $this->queue,
            protokoll: fn (string $zeile) => $this->protokoll[] = $zeile,
            leerlaufPause: 0,
            endeBeiLeerlauf: true, // bei leerer Queue enden statt warten
        );
        $worker->registriere('email', static fn (Auftrag $a) => null);

        $anzahl = $worker->starte();

        self::assertSame(2, $anzahl);
        self::assertSame(2, $this->queue->zaehle('erledigt'));
    }

    #[Test]
    public function fehler_im_handler_bringt_den_worker_nicht_um(): void
    {
        // Der erste Auftrag scheitert (Grenze 1, also gleich tot), der
        // zweite muss danach trotzdem noch abgearbeitet werden.
        $this->queue->lege('bild', ['datei' => 'kaputt.jpg'], maxVersuche: 1);
        $this->queue->lege('email', ['an' => 'ada@example.org']);

        $worker = new Worker(
            queue: $this->queue,
            protokoll: fn (string $zeile) => $this->protokoll[] = $zeile,
            leerlaufPause: 0,
            endeBeiLeerlauf: true,
        );
        $worker->registriere('bild', static function (Auftrag $a): void {
            throw new RuntimeException('Bild nicht lesbar');
        });
        $worker->registriere('email', static fn (Auftrag $a) => null);

        $worker->starte();

        self::assertSame(1, $this->queue->zaehle('fehlgeschlagen'));
        self::assertSame(1, $this->queue->zaehle('erledigt'));
    }

    #[Test]
    public function halteAn_beendet_die_schleife_nach_dem_aktuellen_auftrag(): void
    {
        $this->queue->lege('email', ['an' => 'ada@example.org']);
        $this->queue->lege('email', ['an' => 'grace@example.org']);

        // Dauerbetrieb: Der Worker würde ewig laufen - aber der Handler des
        // ersten Auftrags bittet ihn anzuhalten. Genau das tut ein Signal.
        $worker = new Worker(
            queue: $this->queue,
            protokoll: fn (string $zeile) => $this->protokoll[] = $zeile,
            leerlaufPause: 0,
            endeBeiLeerlauf: false,
        );
        $worker->registriere('email', static function (Auftrag $a) use ($worker): void {
            $worker->halteAn();
        });

        $anzahl = $worker->starte();

        // Genau ein Auftrag lief noch zu Ende; der zweite blieb offen.
        self::assertSame(1, $anzahl);
        self::assertSame(1, $this->queue->zaehle('erledigt'));
        self::assertSame(1, $this->queue->zaehle('offen'));
    }
}
