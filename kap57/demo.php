<?php

declare(strict_types=1);

use App\AppConfig;
use App\ConfigFehler;
use App\Env;
use App\Umgebung;

require __DIR__ . '/vendor/autoload.php';

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Führt den kompletten Weg der Konfiguration vor: aus drei Quellen mit klarer
 * Vorrangfolge in ein typisiertes, unveränderliches AppConfig-Objekt - und
 * zeigt, wie ein Geheimnis dabei niemals im Klartext nach draußen gelangt.
 */

// Die Standardwerte sind die schwächste Quelle (liegen gefahrlos im Code).
$standardwerte = require __DIR__ . '/config/defaults.php';

echo '== 1. Drei Quellen, klare Vorrangfolge ==' . PHP_EOL;

// So kämen die Werte in Wirklichkeit: Standardwerte aus dem Code, dann die
// .env-Datei, zuletzt die echten Umgebungsvariablen (etwa aus einem Container).
// Für ein reproduzierbares Beispiel geben wir die beiden oberen Quellen hier
// als Arrays vor, statt sie aus Datei und Prozessumgebung zu lesen.
$ausDotEnv = [
    'APP_DEBUG' => 'false',
    'MAIL_FROM' => 'team@meine-firma.test',
    'API_KEY'   => 'aus-der-dot-env',
];
$echteUmgebung = [
    'APP_ENV' => 'prod',
    'API_KEY' => 'aus-der-echten-umgebung',
];

$env    = new Env(array_merge($standardwerte, $ausDotEnv, $echteUmgebung));
$config = AppConfig::ausEnv($env);

// alsAnzeige() maskiert das Geheimnis - diese Ausgabe ist gefahrlos.
foreach ($config->alsAnzeige() as $schluessel => $wert) {
    echo "  {$schluessel}: {$wert}" . PHP_EOL;
}

echo PHP_EOL . '== 2. Eine echte Umgebungsdatei einlesen ==' . PHP_EOL;

// Der Parser bewährt sich an der Beispieldatei: Kommentare, Leerzeilen und
// umschließende Anführungszeichen werden korrekt behandelt.
$geparst = Env::parseDatei(__DIR__ . '/.env.example');
foreach ($geparst as $schluessel => $wert) {
    // Auch beim Ausgeben gilt: den API-Schlüssel nie im Klartext zeigen.
    $anzeige = $schluessel === 'API_KEY' ? '***' : $wert;
    echo "  {$schluessel} = {$anzeige}" . PHP_EOL;
}

echo PHP_EOL . '== 3. Pro Umgebung anders verdrahten ==' . PHP_EOL;

// Aus der einen Umgebungsangabe folgt die Wahl der konkreten Technik. Genau
// diese Entscheidung fällt an einer Stelle - hier, nahe der Kompositionswurzel.
$devConfig = AppConfig::ausEnv(new Env(array_merge(
    $standardwerte,
    ['API_KEY' => 'dev-schluessel'],
)));

echo "  prod -> {$config->umgebung->value}: " . waehleMailer($config) . PHP_EOL;
echo "  dev  -> {$devConfig->umgebung->value}: " . waehleMailer($devConfig) . PHP_EOL;

echo PHP_EOL . '== 4. Fehlender Pflichtwert ==' . PHP_EOL;

try {
    // Nur Standardwerte, kein API_KEY - das Geheimnis fehlt vollständig.
    AppConfig::ausEnv(new Env($standardwerte));
} catch (ConfigFehler $fehler) {
    // Die Meldung nennt nur den Schlüssel, nie einen Wert.
    echo '  Abgewiesen: ' . $fehler->getMessage() . PHP_EOL;
}

/**
 * Wählt anhand der Umgebung, welcher Mailversand verdrahtet würde. Ein
 * match über das Enum deckt genau die drei möglichen Fälle ab.
 */
function waehleMailer(AppConfig $config): string
{
    return match ($config->umgebung) {
        Umgebung::Produktion  => 'echter Mailversand (SMTP)',
        Umgebung::Test        => 'gesammelt, kein Versand',
        Umgebung::Entwicklung => 'nur Protokoll ins Log',
    };
}
