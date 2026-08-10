<?php

declare(strict_types=1);

use App\Application\BestellungBezahlenDienst;
use App\Domain\Bestellung;
use App\Domain\Geld;
use App\Event\BestellungBezahlt;
use App\EventDispatcher\EventDispatcher;
use App\Listener\Bestaetigungsmailer;
use App\Listener\Treuepunkte;
use App\Listener\Umsatzstatistik;
use App\ListenerProvider\NachTypProvider;

require __DIR__ . '/vendor/autoload.php';

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Die Kompositionswurzel (Kapitel 52): Hier - und nur hier - werden die
 * Zuhörer am Provider angemeldet und der Dispatcher zusammengesteckt. Der
 * Anwendungsdienst bekommt nur den Dispatcher gereicht; er kennt keinen der
 * drei Zuhörer. Neue Nebenwirkungen ergänzt man mit einer einzigen Zeile
 * lauscheAuf() - ohne den Dienst anzufassen.
 */

// --- Zuhörer bauen ----------------------------------------------------------
$mailer    = new Bestaetigungsmailer();
$statistik = new Umsatzstatistik();
$treue     = new Treuepunkte();

// --- Provider: drei unabhängige Zuhörer auf DASSELBE Ereignis anmelden ------
$provider = new NachTypProvider();
$provider->lauscheAuf(BestellungBezahlt::class, $mailer);
$provider->lauscheAuf(BestellungBezahlt::class, $statistik);
$provider->lauscheAuf(BestellungBezahlt::class, $treue);

// --- Dispatcher und Anwendungsdienst ----------------------------------------
$dispatcher = new EventDispatcher($provider);
$dienst     = new BestellungBezahlenDienst($dispatcher);

// Zwei Bestellungen bezahlen - der Dienst meldet je ein Ereignis.
$dienst->bezahle(new Bestellung(1, 'Ada', Geld::ausEuro(49.90)));
$dienst->bezahle(new Bestellung(2, 'Björn', Geld::ausEuro(250.0)));

echo '--- Bestätigungsmails ---' . PHP_EOL;
foreach ($mailer->versendet as $zeile) {
    echo $zeile . PHP_EOL;
}

echo '--- Statistik ---' . PHP_EOL;
echo "Bezahlte Bestellungen: {$statistik->anzahl()}" . PHP_EOL;
echo "Gesamtumsatz: {$statistik->summe()->alsText()}" . PHP_EOL;

echo '--- Treuepunkte ---' . PHP_EOL;
echo "Ada:   {$treue->fuer('Ada')} Punkte" . PHP_EOL;
echo "Björn: {$treue->fuer('Björn')} Punkte" . PHP_EOL;
