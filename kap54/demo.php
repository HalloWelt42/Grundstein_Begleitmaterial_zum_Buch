<?php

declare(strict_types=1);

// Ein reines Domänen-Skript: Es erzeugt Kunden, Bestellungen und Preise
// nur aus den Objekten der Domäne - ohne Datenbank, ohne HTTP, ohne
// Framework. Genau das ist der Kern dieses Kapitels.

require __DIR__ . '/vendor/autoload.php';

use App\Domain\Bestellposten;
use App\Domain\Bestellung;
use App\Domain\EmailAdresse;
use App\Domain\Geldbetrag;
use App\Domain\Kunde;
use App\Domain\Preisfindung;
use App\Domain\Zeitraum;

// --- Wertobjekte: durch ihren Wert bestimmt, unveränderlich -----------
$preisA = Geldbetrag::inEuro(50);
$preisB = new Geldbetrag(5000, 'EUR');

echo 'Zwei gleiche Geldbeträge: ' . ($preisA->istGleich($preisB) ? 'gleich' : 'verschieden') . PHP_EOL;

// plus() ändert den Betrag nicht, sondern liefert einen neuen.
$summe = $preisA->plus(Geldbetrag::inEuro(25));
echo 'Original bleibt: ' . $preisA->alsText() . ', Summe: ' . $summe->alsText() . PHP_EOL;

// --- Eine E-Mail-Adresse validiert und normalisiert sich selbst -------
$email = new EmailAdresse('  Ada@Example.ORG ');
echo 'Normalisiert: ' . $email->wert . ' (Domain: ' . $email->domain() . ')' . PHP_EOL;

// --- Entities: über ihre Identität bestimmt, veränderlich -------------
$kunde = new Kunde(7, 'Ada Lovelace', $email);
$kunde->aendereEmail(new EmailAdresse('ada@rechenwerk.example'));
$kunde->zumStammkundenMachen();
echo 'Kunde ' . $kunde->id() . ': ' . $kunde->name()
    . ', jetzt ' . $kunde->email()->wert
    . ($kunde->istStammkunde() ? ', Stammkunde' : '') . PHP_EOL;

// --- Ein Aggregat wacht über seine Invarianten ------------------------
$bestellung = new Bestellung(1001, kundenId: 7);
$bestellung->fuegeHinzu(new Bestellposten('Tastatur', Geldbetrag::inEuro(80), 1));
$bestellung->fuegeHinzu(new Bestellposten('Kabel', Geldbetrag::inEuro(5), 4));
echo 'Bestellsumme: ' . $bestellung->gesamtsumme()->alsText()
    . ' (' . count($bestellung->posten()) . ' Posten)' . PHP_EOL;

// --- Ein Domänen-Service für Logik über mehrere Entities --------------
$preisfindung = new Preisfindung();
echo 'Endpreis (Stammkunde): ' . $preisfindung->endpreis($bestellung, $kunde)->alsText() . PHP_EOL;

// Die Invariante schützt vor unsinnigen Zuständen.
$leer = new Bestellung(1002, kundenId: 7);
try {
    $leer->bezahle();
} catch (DomainException $e) {
    echo 'Abgewiesen: ' . $e->getMessage() . PHP_EOL;
}

// --- Ein Zeitraum als Wertobjekt --------------------------------------
$aktion = new Zeitraum(
    new DateTimeImmutable('2026-08-01'),
    new DateTimeImmutable('2026-08-31'),
);
$stichtag = new DateTimeImmutable('2026-08-10');
echo 'Aktion läuft ' . $aktion->tage() . ' Tage, Stichtag im Zeitraum: '
    . ($aktion->enthaelt($stichtag) ? 'ja' : 'nein') . PHP_EOL;
