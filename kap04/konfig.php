<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 4: Die aktive Konfiguration abfragen.
 *
 * Nicht raten, sondern nachsehen: ini_get() liest den aktiven Wert
 * vieler Einstellungen direkt aus dem laufenden Skript heraus.
 * Ein paar Direktiven (etwa error_reporting) sind ein Sonderfall und
 * brauchen ihre eigene Funktion - dazu unten mehr.
 * php_ini_loaded_file() verrät, welche php.ini überhaupt geladen wurde.
 */

// Welche php.ini ist aktiv? (false = gar keine geladen, nur Standardwerte)
$iniDatei = php_ini_loaded_file();

echo 'PHP-Version:  ' . PHP_VERSION . PHP_EOL;
echo 'Betriebsart:  ' . PHP_SAPI . PHP_EOL;   // z. B. "cli" oder "cli-server"
echo 'php.ini:      ' . ($iniDatei !== false ? $iniDatei : '(keine geladen)') . PHP_EOL;
echo str_repeat('-', 48) . PHP_EOL;

/*
 * Eine kleine Auswahl der Einstellungen, die im Alltag am häufigsten
 * zählen. Für diese gibt ini_get() den aktiven Wert als Zeichenkette
 * zurück.
 */
$einstellungen = [
    'memory_limit',
    'max_execution_time',
    'display_errors',
    'log_errors',
    'date.timezone',
    'opcache.enable',
    'opcache.enable_cli',
];

foreach ($einstellungen as $name) {
    $wert = ini_get($name);
    // false = unbekannte Direktive; leerer String = nicht gesetzt.
    $anzeige = ($wert === false || $wert === '') ? '(leer)' : $wert;
    printf('%-20s = %s%s', $name, $anzeige, PHP_EOL);
}

/*
 * Sonderfall error_reporting: Für diese Direktive liefert ini_get()
 * nur die rohe ini-Zeichenkette - im schlanken Container ist die leer.
 * Der WIRKLICH aktive Meldelevel steht als Zahl in error_reporting();
 * hier entspricht er E_ALL (alles melden), der Voreinstellung von PHP 8.4.
 */
$level = error_reporting();
printf(
    '%-20s = %d (E_ALL: %s)%s',
    'error_reporting',
    $level,
    $level === E_ALL ? 'ja' : 'nein',
    PHP_EOL,
);
