<?php

declare(strict_types=1);

use App\Event\KundeBenachrichtigen;
use App\EventDispatcher\EventDispatcher;
use App\Listener\PushKanal;
use App\Listener\SmsKanal;
use App\ListenerProvider\NachTypProvider;

require __DIR__ . '/vendor/autoload.php';

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Stoppbare Ereignisse in Aktion: Der Kunde soll über den ersten verfügbaren
 * Kanal benachrichtigt werden. Push ist vor SMS angemeldet und beendet nach
 * erfolgreicher Zustellung die Verbreitung - der SMS-Kanal kommt gar nicht
 * mehr an die Reihe.
 */

$push = new PushKanal();
$sms  = new SmsKanal();

// Reihenfolge = Vorrang: zuerst Push, dann SMS.
$provider = new NachTypProvider();
$provider->lauscheAuf(KundeBenachrichtigen::class, $push);
$provider->lauscheAuf(KundeBenachrichtigen::class, $sms);

$dispatcher = new EventDispatcher($provider);

$dispatcher->dispatch(new KundeBenachrichtigen('Ada', 'Deine Bestellung ist bezahlt.'));

echo 'Push-Kanal: ' . count($push->zugestellt) . ' zugestellt' . PHP_EOL;
foreach ($push->zugestellt as $zeile) {
    echo '  ' . $zeile . PHP_EOL;
}

echo 'SMS-Kanal:  ' . count($sms->zugestellt) . ' zugestellt (übersprungen)' . PHP_EOL;
