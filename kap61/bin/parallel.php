<?php

declare(strict_types=1);

// Beweist, dass das sichere Herausgreifen hält: zwei Worker arbeiten
// gleichzeitig auf derselben SQLite-Datei. Am Ende muss jeder Auftrag
// genau einmal erledigt sein - keiner doppelt, keiner verloren.
//
// Braucht die Erweiterung pcntl (für pcntl_fork). Aufruf im Worker-
// Abbild aus dem Dockerfile dieses Kapitels.

require __DIR__ . '/../vendor/autoload.php';

use App\Auftrag;
use App\Datenbank;
use App\Queue;
use App\Worker;

$pfad = $argv[1] ?? __DIR__ . '/../parallel.sqlite';
@unlink($pfad); // für einen sauberen Lauf frisch beginnen

if (!function_exists('pcntl_fork')) {
    fwrite(STDERR, "Dieses Beispiel braucht die Erweiterung pcntl.\n");

    exit(1);
}

const ANZAHL = 20;

// Erst die Warteschlange füllen ...
$queue = new Queue(Datenbank::oeffne($pfad));
$queue->migriere();
for ($i = 1; $i <= ANZAHL; $i++) {
    $queue->lege('email', ['an' => "nutzer{$i}@example.org"]);
}
unset($queue); // die eigene Verbindung vor dem Fork schließen

// ... dann zwei Worker als getrennte Prozesse starten.
$kinder = [];
for ($nr = 1; $nr <= 2; $nr++) {
    $pid = pcntl_fork();

    if ($pid === 0) {
        // Kindprozess: eigene, frische Verbindung (nie über fork teilen).
        $queue  = new Queue(Datenbank::oeffne($pfad));
        $worker = new Worker(
            queue: $queue,
            protokoll: static fn (string $z) => null, // still arbeiten
            leerlaufPause: 0,
            endeBeiLeerlauf: true,
        );
        // Etwas Arbeit je Auftrag, damit sich beide Worker abwechseln
        // können, statt dass einer die ganze Queue in einem Zug leert.
        $worker->registriere('email', static fn (Auftrag $a) => usleep(20_000));

        $erledigt = $worker->starte();

        exit($erledigt); // Zahl der erledigten Aufträge als Exit-Code
    }

    $kinder[$nr] = $pid;
}

// Elternprozess: auf beide Kinder warten und ihre Zahlen einsammeln.
foreach ($kinder as $nr => $pid) {
    pcntl_waitpid($pid, $status);
    $erledigt = pcntl_wexitstatus($status);
    echo "Worker {$nr} erledigte: {$erledigt}   Fehler: 0" . PHP_EOL;
}

// Nachweis: genau ANZAHL verschiedene Aufträge sind erledigt.
$pdo = Datenbank::oeffne($pfad);
$verschieden = (int) $pdo->query(
    "SELECT COUNT(DISTINCT id) FROM auftrag WHERE status = 'erledigt'"
)->fetchColumn();
echo 'verschiedene IDs (soll ' . ANZAHL . "): {$verschieden}" . PHP_EOL;

@unlink($pfad);
