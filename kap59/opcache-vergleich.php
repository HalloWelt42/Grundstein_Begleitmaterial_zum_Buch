<?php

declare(strict_types=1);

/*
 * Der eigentliche Vergleich: Wie viel spart der OPcache über viele Anfragen?
 *
 * Auf der Kommandozeile gibt es keinen dauerhaften Serverprozess, in dem die
 * kompilierten Opcodes zwischen den Anfragen überleben könnten. Als Ersatz
 * für den gemeinsamen Speicher eines echten Servers nutzen wir den
 * Datei-Cache des OPcache (file_cache): Er legt die Opcodes auf der Platte
 * ab, sodass sie den einzelnen Prozessstart überdauern.
 *
 * Wichtig ist eine ehrliche Messung. Auf einem echten Server kompiliert die
 * ERSTE Anfrage nach dem Ausrollen einmal und füllt den Cache; alle WEITEREN
 * Anfragen finden die Opcodes fertig vor. Genau diesen eingeschwungenen
 * Zustand messen wir: Ein paar Aufwärmläufe (die auch den Cache füllen)
 * zählen nicht mit, danach mitteln wir über ANFRAGEN echte Läufe.
 */

const AUFWAERMEN = 3;
const ANFRAGEN   = 60;

/**
 * Startet last.php einmal als eigenen Prozess und liefert die von ihm
 * gemessene Ladezeit in Millisekunden zurück.
 *
 * @param list<string> $flags zusätzliche -d-Schalter für den PHP-Aufruf
 */
function laufeEinmal(array $flags): float
{
    $befehl = escapeshellarg(PHP_BINARY);
    foreach ($flags as $flag) {
        $befehl .= ' ' . escapeshellarg($flag);
    }
    $befehl .= ' ' . escapeshellarg(__DIR__ . '/last.php');

    $zeilen = [];
    exec($befehl, $zeilen);

    $letzte = $zeilen === [] ? '0' : (string) end($zeilen);

    return (float) trim($letzte);
}

/**
 * Wärmt mit AUFWAERMEN Läufen vor (verworfen) und mittelt danach über
 * ANFRAGEN echte Läufe die Ladezeit je Anfrage in Millisekunden.
 *
 * @param list<string> $flags
 */
function mittelUeberAnfragen(array $flags): float
{
    for ($i = 0; $i < AUFWAERMEN; $i++) {
        laufeEinmal($flags); // füllt bei der Cache-Variante den Datei-Cache
    }

    $summe = 0.0;
    for ($i = 0; $i < ANFRAGEN; $i++) {
        $summe += laufeEinmal($flags);
    }

    return $summe / ANFRAGEN;
}

// --- Ohne OPcache: jede Anfrage parst und kompiliert neu -------------------
$ohne = mittelUeberAnfragen(['-dopcache.enable_cli=0']);

// --- Mit OPcache (Datei-Cache): ab der zweiten Anfrage nur noch ausführen ---
$cacheDir = sys_get_temp_dir() . '/kap59-opcache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

$mit = mittelUeberAnfragen([
    '-dopcache.enable_cli=1',
    '-dopcache.file_cache=' . $cacheDir,
    '-dopcache.file_cache_only=1',
    '-dopcache.validate_timestamps=0',
]);

printf('ohne OPcache: %.1f ms je Anfrage%s', $ohne, PHP_EOL);
printf('mit OPcache:  %.1f ms je Anfrage%s', $mit, PHP_EOL);
