<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Bestellungen;
use App\Domain\Bestellstatus;
use App\Domain\Bestellung;
use App\Domain\EmailAdresse;
use App\Domain\Geldbetrag;
use PDO;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Der getriebene Adapter für die Produktion (Kapitel 55). Er erfüllt den Port
 * Bestellungen und spricht dahinter über PDO mit einer echten Datenbank. Die
 * Richtung der Abhängigkeit ist entscheidend: Dieser Adapter importiert die
 * Anwendung und die Domäne - nie umgekehrt. In keiner Datei des Kerns steht
 * ein use auf die Infrastruktur. Das SQL lebt ausschließlich hier.
 */
final class PdoBestellungen implements Bestellungen
{
    // Die Verbindung wird von außen hereingereicht (Komposition, Kapitel 15).
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    public function find(int $id): ?Bestellung
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, kunde, betrag_cent, waehrung, status
               FROM bestellung WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        $zeile = $stmt->fetch();

        // Kein Treffer wird ein sauberes null - nicht das PDO-eigene false.
        return $zeile === false ? null : $this->aufBestellung($zeile);
    }

    public function save(Bestellung $bestellung): Bestellung
    {
        // Noch keine id: einfügen und die vergebene id nachreichen.
        if ($bestellung->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO bestellung (kunde, betrag_cent, waehrung, status)
                 VALUES (:kunde, :betrag, :waehrung, :status)'
            );
            $stmt->execute([
                'kunde'    => $bestellung->kunde->wert,
                'betrag'   => $bestellung->betrag->cent,
                'waehrung' => $bestellung->betrag->waehrung,
                'status'   => $bestellung->status->value,
            ]);

            // Bestellung ist unveränderlich: ein NEUES Objekt mit gefüllter id.
            return new Bestellung(
                (int) $this->pdo->lastInsertId(),
                $bestellung->kunde,
                $bestellung->betrag,
                $bestellung->status,
            );
        }

        // Vorhandene id: aktualisieren.
        $stmt = $this->pdo->prepare(
            'UPDATE bestellung
                SET kunde = :kunde, betrag_cent = :betrag,
                    waehrung = :waehrung, status = :status
              WHERE id = :id'
        );
        $stmt->execute([
            'id'       => $bestellung->id,
            'kunde'    => $bestellung->kunde->wert,
            'betrag'   => $bestellung->betrag->cent,
            'waehrung' => $bestellung->betrag->waehrung,
            'status'   => $bestellung->status->value,
        ]);

        return $bestellung;
    }

    public function alle(): array
    {
        // Feste Abfrage ohne Werte von außen - query() genügt.
        $stmt = $this->pdo->query(
            'SELECT id, kunde, betrag_cent, waehrung, status
               FROM bestellung ORDER BY id'
        );

        // Jede Zeile wandert durch dieselbe Mapping-Methode.
        return array_map($this->aufBestellung(...), $stmt->fetchAll());
    }

    /**
     * Das Herzstück der Kapselung: rohe Zeile rein, Domänen-Objekt raus.
     *
     * @param array<string, mixed> $zeile
     */
    private function aufBestellung(array $zeile): Bestellung
    {
        return new Bestellung(
            (int) $zeile['id'],
            new EmailAdresse((string) $zeile['kunde']),
            new Geldbetrag((int) $zeile['betrag_cent'], (string) $zeile['waehrung']),
            Bestellstatus::from((string) $zeile['status']),
        );
    }
}
