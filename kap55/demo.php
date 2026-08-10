<?php

declare(strict_types=1);

use App\Adapter\Http\BestellungController;
use App\Adapter\Payment\BegrenzteZahlung;
use App\Adapter\Persistence\PdoBestellungen;
use App\Application\BestellungBezahlenDienst;
use App\Domain\Bestellung;
use App\Domain\Geld;

require __DIR__ . '/vendor/autoload.php';

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Die Kompositionswurzel am Rand der Anwendung: NUR hier werden die echten
 * Adapter zusammengesteckt. Genau an dieser einen Stelle entscheidet sich,
 * welche Technik hinter jedem Port steckt. Der Dienst im Zentrum bleibt davon
 * unberührt - er sieht ausschließlich seine Ports.
 */

// --- getriebene Adapter (secondary) -----------------------------------------
$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
$pdo->exec((string) file_get_contents(__DIR__ . '/src/Adapter/Persistence/schema.sql'));

$bestellungen = new PdoBestellungen($pdo);                 // Persistenz per Datenbank
$zahlung      = new BegrenzteZahlung(Geld::ausEuro(100.0)); // Zahlung mit 100-EUR-Limit

// --- Sechseck-Inneres --------------------------------------------------------
$dienst = new BestellungBezahlenDienst($bestellungen, $zahlung);

// --- treibender Adapter (primary) -------------------------------------------
$controller = new BestellungController($dienst);

// Zwei offene Bestellungen anlegen: eine unter, eine über dem Limit.
$klein = $bestellungen->save(Bestellung::neu('Ada', Geld::ausEuro(49.90)));
$gross = $bestellungen->save(Bestellung::neu('Björn', Geld::ausEuro(250.0)));

echo 'Offene Bestellungen vor der Zahlung: ' . count($bestellungen->alleOffenen()) . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

// Der treibende Adapter verarbeitet drei Anfragen, als kämen sie per HTTP:
// die kleine Bestellung, die große und eine unbekannte id.
foreach ([$klein->id, $gross->id, 999] as $id) {
    $antwort = $controller->bezahlen(['id' => $id]);

    echo "POST /bestellungen/{$id}/bezahlung  ->  {$antwort['status']}" . PHP_EOL;
    echo '  ' . json_encode($antwort['body'], JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

echo str_repeat('-', 60) . PHP_EOL;
echo 'Offene Bestellungen nach der Zahlung: ' . count($bestellungen->alleOffenen()) . PHP_EOL;
