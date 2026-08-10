<?php

declare(strict_types=1);

use App\AppConfig;
use App\Env;

require __DIR__ . '/vendor/autoload.php';

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Der Bootstrap am Rand der Anwendung: Er liest die Konfiguration aus den drei
 * echten Quellen und baut daraus das typisierte AppConfig. Genau so stellt ein
 * Front Controller (Kapitel 43) oder ein Container (Kapitel 52) die
 * Konfiguration bereit - einmal beim Start, danach unveränderlich.
 *
 * In einem Container werden die Werte per Umgebungsvariable hereingereicht
 * (docker run -e APP_ENV=prod ...). getenv() liest genau diese.
 */

$env = Env::aus(
    require __DIR__ . '/config/defaults.php', // Standardwerte aus dem Code
    __DIR__ . '/.env',                        // lokale .env (fehlt in Produktion)
    getenv(),                                 // echte Umgebungsvariablen (z. B. Container)
);

$config = AppConfig::ausEnv($env);

// Nur die maskierte Anzeige ausgeben - kein Geheimnis im Klartext.
foreach ($config->alsAnzeige() as $schluessel => $wert) {
    echo "{$schluessel}: {$wert}" . PHP_EOL;
}
