<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 4: Ein Fehler, zwei Betriebsarten.
 *
 * Dieses Skript löst bewusst eine Warnung aus. Ob du sie zu sehen
 * bekommst und wohin sie geht, entscheidet NICHT das Skript, sondern
 * die aktive Konfiguration (php.ini oder -d-Schalter beim Aufruf).
 * So kannst du denselben Code einmal im Entwicklungs- und einmal im
 * Produktionsmodus starten und den Unterschied selbst sehen.
 */

// Zeigt zuerst, in welcher Betriebsart wir gerade laufen.
printf(
    'display_errors=%s | log_errors=%s%s',
    ini_get('display_errors') ?: '0',
    ini_get('log_errors') ?: '0',
    PHP_EOL,
);

echo 'Vor dem Fehler.' . PHP_EOL;

// Zugriff auf einen Schlüssel, den es nicht gibt: PHP meldet eine
// Warnung. Solche Flüchtigkeitsfehler sind der häufigste Grund,
// warum man die Fehleranzeige während der Entwicklung braucht.
$konfig = ['name' => 'Grundstein'];
$version = $konfig['version'];   // 'version' fehlt -> Warning: Undefined array key

echo 'Nach dem Fehler - das Skript läuft weiter.' . PHP_EOL;
