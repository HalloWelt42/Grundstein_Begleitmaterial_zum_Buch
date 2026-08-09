<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 27: Logging
 *
 * Nutzung des eigenen PSR-3-nahen Loggers. Der Dienst bekommt seinen
 * Logger per Konstruktor hineingereicht (Dependency Injection) und
 * typisiert dabei auf das LoggerInterface, nie auf die konkrete Klasse -
 * so bleibt er unabhängig davon, wohin eigentlich geschrieben wird.
 * Nachrichten sind feste Vorlagen mit Platzhaltern samt Kontext-Array.
 * Die Schwelle steht auf info, sodass debug-Zeilen unterdrückt,
 * wichtigere Stufen aber sicher geschrieben werden.
 *
 * Alle Ausgaben im Buch stammen aus einem echten Lauf mit PHP 8.4.
 */

use App\Log\EinfacherLogger;
use App\Log\LoggerInterface;

require __DIR__ . '/logger.php';

/**
 * Ein Dienst, der Anmeldungen verbucht. Er kennt nur das LoggerInterface
 * und erzeugt den Logger nicht selbst - so bleibt der Dienst frei von der
 * Frage, welcher Logger es ist und wohin geschrieben wird.
 */
final class Anmeldedienst
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function anmelden(int $id, string $name): void
    {
        // Die Nachricht bleibt eine feste Vorlage, die Werte stehen im Kontext.
        $this->logger->info('Nutzer {name} (#{id}) angemeldet', [
            'id'   => $id,
            'name' => $name,
        ]);
    }

    public function fehlversuch(string $name): void
    {
        $this->logger->warning('Fehlgeschlagene Anmeldung für {name}', [
            'name' => $name,
        ]);
    }
}

// Der Logger schreibt hier auf die Standardausgabe. In Produktion wäre
// das Ziel eine Datei oder ein zentraler Sammeldienst.
$logger = new EinfacherLogger(STDOUT, mindeststufe: 'info');

$dienst = new Anmeldedienst($logger);
$dienst->anmelden(42, 'Ada');
$dienst->fehlversuch('unbekannt');

// Diese debug-Zeile taucht wegen der Schwelle info gar nicht erst auf.
$logger->debug('Interner Zwischenstand: {schritt}', ['schritt' => 3]);

// Ein kritischer Fehler dagegen wird immer geschrieben.
$logger->critical('Datenbank nicht erreichbar');
