<?php

declare(strict_types=1);

// Der Worker: eine lange laufende Schleife, die Aufträge abarbeitet.
//
// Aufruf:
//   php bin/arbeite.php <dbpfad>          - endet bei leerer Queue (Demo)
//   php bin/arbeite.php <dbpfad> dauer    - Dauerbetrieb, langsame Aufträge
//                                           (für die Signal-Vorführung)

require __DIR__ . '/../vendor/autoload.php';

use App\Auftrag;
use App\Datenbank;
use App\Queue;
use App\Worker;

$pfad  = $argv[1] ?? __DIR__ . '/../auftraege.sqlite';
$dauer = ($argv[2] ?? '') === 'dauer';

// Jede Protokollzeile bekommt einen Zeitstempel vorangestellt.
$protokoll = static function (string $zeile): void {
    echo '[' . date('H:i:s') . '] ' . $zeile . PHP_EOL;
};

$queue  = new Queue(Datenbank::oeffne($pfad));
$worker = new Worker(
    queue: $queue,
    protokoll: $protokoll,
    leerlaufPause: 1,
    endeBeiLeerlauf: !$dauer, // im Dauerbetrieb bei Leerlauf warten
);

// Handler je Auftragstyp. Sie simulieren die eigentliche Arbeit; im
// Dauerbetrieb dauert jeder Auftrag etwa eine Sekunde, damit sich ein
// Signal mitten in der Bearbeitung vorführen lässt.
$arbeit = static function () use ($dauer): void {
    $dauer ? sleep(1) : usleep(150_000);
};
$worker->registriere('email',   static fn (Auftrag $a) => $arbeit());
$worker->registriere('bild',    static fn (Auftrag $a) => $arbeit());
$worker->registriere('bericht', static fn (Auftrag $a) => $arbeit());

// --- Sauberes Beenden auf Signal (graceful shutdown) ---
if (function_exists('pcntl_async_signals')) {
    // Signale sofort zustellen, ohne dass wir pcntl_signal_dispatch() rufen.
    pcntl_async_signals(true);

    $anhalten = static function (int $signal) use ($worker, $protokoll): void {
        $protokoll("Signal {$signal} empfangen - beende nach dem aktuellen Auftrag.");
        $worker->halteAn();
    };

    pcntl_signal(SIGTERM, $anhalten); // vom Orchestrierer beim Stoppen
    pcntl_signal(SIGINT, $anhalten);  // von Strg+C im Terminal
}

$protokoll('Worker gestartet.');
$anzahl = $worker->starte();
$protokoll("Worker beendet - {$anzahl} Aufträge in dieser Runde verarbeitet.");
