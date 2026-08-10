<?php

declare(strict_types=1);

use App\Container;
use App\Logger;
use App\Mailer;
use App\ProtokollMailer;
use App\Registrierung;
use App\SammelLogger;

require __DIR__ . '/vendor/autoload.php';

$container = new Container();

// Die Kompositionswurzel: hier - und nur hier - wird verdrahtet. Jede
// Fabrik beschreibt, WIE ein Dienst gebaut wird, und holt sich seine
// Abhängigkeiten über denselben Container.
$container->set(Logger::class, fn(): Logger => new SammelLogger());

$container->set(Mailer::class, fn(Container $c): Mailer => new ProtokollMailer(
    $c->get(Logger::class),
));

$container->set(Registrierung::class, fn(Container $c): Registrierung => new Registrierung(
    $c->get(Mailer::class),
    $c->get(Logger::class),
));

// Ab hier fragt die Anwendung nur noch fertige Dienste ab. Sie muss
// nicht wissen, wie viele Objekte dahinter zusammengesteckt werden.
$registrierung = $container->get(Registrierung::class);

echo $registrierung->registriere('ada@example.org') . PHP_EOL;
echo $registrierung->registriere('grace@example.org') . PHP_EOL;

// Der Logger ist genau eine Instanz: Dienst UND Mailer schrieben hinein.
echo '--- Protokoll ---' . PHP_EOL;
$logger = $container->get(Logger::class);
if ($logger instanceof SammelLogger) {
    foreach ($logger->alleZeilen() as $zeile) {
        echo $zeile . PHP_EOL;
    }
}
