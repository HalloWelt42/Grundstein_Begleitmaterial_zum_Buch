<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 2: Ein Blick in die Umgebung.
 *
 * Dieses Skript beantwortet eine einzige Frage: Was steckt eigentlich in
 * dem Container, in dem unser PHP läuft? Es zeigt die Version, die
 * Schnittstelle (SAPI) und prüft, ob die Erweiterungen vorhanden sind,
 * die wir im Lauf des Buches brauchen. So siehst du auf einen Blick, ob
 * deine Umgebung bereit ist.
 */

/**
 * Gibt eine Zeile im Format "Name: Wert" aus, sauber ausgerichtet.
 *
 * @param string $name  Bezeichnung des Merkmals (z. B. "PHP-Version").
 * @param string $wert  Der zugehörige Wert.
 */
function zeile(string $name, string $wert): void
{
    // %-24s = linksbündig auf 24 Zeichen aufgefüllt, dann der Wert.
    printf('%-24s %s' . PHP_EOL, $name . ':', $wert);
}

/**
 * Meldet in Worten, ob eine PHP-Erweiterung geladen ist.
 *
 * @param string $erweiterung  Interner Name der Erweiterung (z. B. "pdo_sqlite").
 * @return string              "vorhanden", wenn geladen, sonst "FEHLT".
 */
function status(string $erweiterung): string
{
    return extension_loaded($erweiterung) ? 'vorhanden' : 'FEHLT';
}

// --- Kerndaten der Umgebung ------------------------------------------
zeile('PHP-Version', PHP_VERSION);
zeile('Schnittstelle (SAPI)', PHP_SAPI);
zeile('Betriebssystem', PHP_OS_FAMILY);
zeile('Speicherlimit', (string) ini_get('memory_limit'));

// --- Erweiterungen, die wir im Buch immer wieder brauchen ------------
echo PHP_EOL . 'Wichtige Erweiterungen:' . PHP_EOL;

// Diese Liste wächst nicht ins Uferlose - es sind genau die, auf die
// wir uns später stützen: Zeichenketten, JSON, Datenbank, Netzwerk.
$benoetigt = ['mbstring', 'json', 'pdo_sqlite', 'curl', 'openssl'];

foreach ($benoetigt as $name) {
    zeile('  ' . $name, status($name));
}
