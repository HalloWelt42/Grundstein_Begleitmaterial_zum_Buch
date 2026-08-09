<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 26: Fehler und Ausnahmen
 *
 * Teil 4: Eigene Ausnahme-Klassen. Sie entstehen durch extends einer
 * passenden Basis (hier RuntimeException) und tragen zusätzlichen
 * Kontext - Daten, die der Fangende zum Reagieren braucht. Eine kleine
 * Hierarchie mit einer gemeinsamen Basis erlaubt es, alle Fehler eines
 * Bereichs mit einem einzigen catch zu fangen.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/**
 * Gemeinsame Basis für alle Fehler rund um das Konto. Wer diese Klasse
 * fängt, fängt jeden Konto-Fehler auf einmal.
 */
abstract class KontoException extends RuntimeException
{
}

/**
 * Das Konto hat nicht genug Deckung. Trägt als Kontext den Fehlbetrag,
 * damit der Aufrufer eine sinnvolle Meldung bauen kann.
 */
final class DeckungException extends KontoException
{
    public function __construct(
        public readonly int $benoetigtCent,
        public readonly int $verfuegbarCent,
    ) {
        $fehlbetrag = $this->benoetigtCent - $this->verfuegbarCent;
        // Der Basis-Konstruktor bekommt eine lesbare Meldung.
        parent::__construct(
            sprintf('Es fehlen %.2f Euro.', $fehlbetrag / 100),
        );
    }
}

/**
 * Der angeforderte Betrag ergibt keinen Sinn (etwa null oder negativ).
 */
final class BetragException extends KontoException
{
}

/**
 * Ein einfaches Konto, das seinen Stand in Cent führt und je nach Lage
 * eine der beiden Konto-Ausnahmen wirft.
 */
final class Konto
{
    public function __construct(private int $standCent)
    {
    }

    public function standCent(): int
    {
        return $this->standCent;
    }

    public function abheben(int $betragCent): void
    {
        if ($betragCent <= 0) {
            throw new BetragException('Der Betrag muss positiv sein.');
        }

        if ($betragCent > $this->standCent) {
            throw new DeckungException($betragCent, $this->standCent);
        }

        $this->standCent -= $betragCent;
    }
}

$konto = new Konto(5000);

foreach ([-100, 8000, 2000] as $betrag) {
    try {
        $konto->abheben($betrag);
        printf('Abgehoben: %.2f Euro, neuer Stand %.2f Euro.' . PHP_EOL,
            $betrag / 100, $konto->standCent() / 100);
    } catch (DeckungException $fehler) {
        // Diese Unterklasse liefert Kontext, den wir gezielt auslesen.
        printf('Deckung fehlt: %s (verfügbar %.2f Euro)' . PHP_EOL,
            $fehler->getMessage(), $fehler->verfuegbarCent / 100);
    } catch (KontoException $fehler) {
        // Alle übrigen Konto-Fehler landen hier.
        echo 'Konto-Fehler: ' . $fehler->getMessage() . PHP_EOL;
    }
}
