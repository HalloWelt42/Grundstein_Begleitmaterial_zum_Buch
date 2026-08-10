<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Abonnent;
use App\Domain\AbonnentRepository;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Die Anwendungsschicht: der Anwendungsfall "Zum Newsletter anmelden".
 * Dieser Dienst orchestriert die Fachregeln in der richtigen Reihenfolge,
 * kennt aber weder HTTP noch SQL. Seine Abhängigkeiten - der
 * Repository-Vertrag und die Uhr - kommen über den Konstruktor herein
 * (Dependency Injection aus Kapitel 28 und 49), nie per new in der Logik.
 */
final class AnmeldeService
{
    public function __construct(
        private readonly AbonnentRepository $abonnenten,
        private readonly Clock $uhr,
    ) {}

    /**
     * Führt den Anwendungsfall aus und gibt den gespeicherten Abonnenten
     * zurück. Bei verletzten Fachregeln wirft er einen AnmeldeFehler.
     */
    public function meldeAn(Anmeldebefehl $befehl): Abonnent
    {
        // Adresse vereinheitlichen: ohne Rand-Leerzeichen, klein.
        $email = strtolower(trim($befehl->email));

        // Fachregel 1: Die Adresse muss syntaktisch gültig sein.
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new UngueltigeEmail("Keine gültige Adresse: {$befehl->email}");
        }

        // Fachregel 2: Keine Adresse darf doppelt angemeldet werden.
        if ($this->abonnenten->existiertMit($email)) {
            throw new BereitsAngemeldet("Bereits angemeldet: {$email}");
        }

        // Alles in Ordnung: Abonnenten anlegen und speichern lassen.
        return $this->abonnenten->speichere(
            Abonnent::neu($email, $this->uhr->jetzt()),
        );
    }
}
