<?php

declare(strict_types=1);

use App\Application\BestellungAufgeben;
use App\Application\BestellungAufgebenDienst;
use App\Application\Bestellungen;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Container\Container;
use App\Infrastructure\Listener\BestaetigungProtokollieren;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Eine CLI-Demo, die die ganze Anwendung ohne Webserver vorführt. Sie baut den
 * Container an der Kompositionswurzel auf und ruft danach nur noch fertige
 * Dienste ab - genau wie der Front Controller, nur von der Kommandozeile aus.
 * Ohne .env greifen die Standardwerte: Entwicklung und SQLite im Speicher.
 */
require __DIR__ . '/vendor/autoload.php';

/** @var callable(?string): Container $bootstrap */
$bootstrap = require __DIR__ . '/config/bootstrap.php';
$container = $bootstrap(__DIR__ . '/.env');

// Die Konfiguration zeigen (typisiert aus der Umgebung gelesen).
$config = $container->get(Config::class);
echo "{$config->appName} - Umgebung: {$config->umgebung->value}" . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

// Der Anwendungsfall: zwei gültige Bestellungen und eine ungültige Adresse.
$dienst = $container->get(BestellungAufgebenDienst::class);

foreach ([
    ['ada@example.org', 49.90],
    ['grace@example.org', 250.0],
] as [$kunde, $euro]) {
    $bestellung = $dienst->fuehreAus(new BestellungAufgeben($kunde, $euro));
    echo sprintf(
        "Aufgegeben: #%d  %-18s %10s  [%s]" . PHP_EOL,
        $bestellung->id ?? 0,
        $bestellung->kunde->wert,
        $bestellung->betrag->alsText(),
        $bestellung->status->value,
    );
}

// Der Weg über den Repository-Port zurück: alles frisch aus der Datenbank.
$alle = $container->get(Bestellungen::class)->alle();
echo str_repeat('-', 60) . PHP_EOL;
echo 'Bestellungen in der Datenbank: ' . count($alle) . PHP_EOL;

// Die Reaktion auf die Ereignisse: der Zuhörer hat je eine Bestätigung notiert.
$zuhoerer = $container->get(BestaetigungProtokollieren::class);
echo str_repeat('-', 60) . PHP_EOL;
echo 'Verschickte Bestätigungen (per Ereignis):' . PHP_EOL;
foreach ($zuhoerer->protokoll as $zeile) {
    echo '  ' . $zeile . PHP_EOL;
}
