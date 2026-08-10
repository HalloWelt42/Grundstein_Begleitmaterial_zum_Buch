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

// Diesmal registrieren wir NUR die Interface-Bindungen: Welche konkrete
// Klasse steckt hinter Logger, welche hinter Mailer? Alles Weitere - das
// eigentliche Zusammenstecken - erledigt das Autowiring per Reflection.
$container->set(Logger::class, fn(Container $c): Logger => $c->get(SammelLogger::class));
$container->set(Mailer::class, fn(Container $c): Mailer => $c->get(ProtokollMailer::class));

// Registrierung wurde NIE registriert. Der Container liest ihren
// Konstruktor, sieht die Typen Mailer und Logger und baut den ganzen
// Graphen selbst zusammen.
$registrierung = $container->get(Registrierung::class);

echo $registrierung->registriere('ada@example.org') . PHP_EOL;

// Auch beim Autowiring bleibt der SammelLogger ein einziges, geteiltes
// Objekt - hier liegen beide Protokollzeilen beisammen.
$logger = $container->get(SammelLogger::class);
if ($logger instanceof SammelLogger) {
    echo count($logger->alleZeilen()) . ' Protokollzeilen.' . PHP_EOL;
}
