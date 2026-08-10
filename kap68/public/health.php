<?php

declare(strict_types=1);

use App\HealthCheck;

/*
 * Der Gesundheits-Endpunkt. Ein Reverse-Proxy oder ein Orchestrierer ruft
 * ihn auf, um zu erfahren, ob diese Instanz Anfragen annehmen darf. Er
 * hält sich bewusst kurz und spricht keine langsamen fremden Dienste an -
 * eine Gesundheitsprüfung, die selbst hängt, ist schlimmer als keine.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

$check = new HealthCheck(
    version: getenv('APP_VERSION') ?: 'unbekannt',
    pruefungen: [
        // Läuft der Opcode-Cache? Im Betrieb ein Muss (Kapitel 59).
        'opcache' => static fn (): bool => function_exists('opcache_get_status')
            && (opcache_get_status(false)['opcache_enabled'] ?? false),
        // Ist das Schreibverzeichnis wirklich beschreibbar?
        'tmp'     => static fn (): bool => is_writable(sys_get_temp_dir()),
    ],
);

$ergebnis = $check->ausfuehren();

// Ein kranker Zustand meldet 503 - so nimmt ein Reverse-Proxy die Instanz
// von selbst aus dem Verkehr, ohne dass ein Mensch eingreifen muss.
http_response_code($ergebnis['status'] === 'ok' ? 200 : 503);
header('Content-Type: application/json');

echo json_encode($ergebnis, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . PHP_EOL;
