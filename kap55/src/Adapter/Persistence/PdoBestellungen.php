<?php

declare(strict_types=1);

namespace App\Adapter\Persistence;

use App\Application\Bestellungen;
use App\Domain\Bestellung;
use App\Domain\Geld;
use PDO;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Ein GETRIEBENER Adapter für die Produktion. Er erfüllt den Port Bestellungen
 * und spricht dahinter über PDO mit einer echten Datenbank. Entscheidend ist
 * die Richtung der Abhängigkeit: Dieser Adapter importiert die Anwendung
 * (App\Application\Bestellungen) und die Domäne - nie umgekehrt. In keiner
 * Datei des Kerns steht ein use auf App\Adapter. Das SQL lebt ausschließlich
 * hier.
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
            'SELECT id, kunde, betrag_cent, waehrung, bezahlt
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
                'INSERT INTO bestellung (kunde, betrag_cent, waehrung, bezahlt)
                 VALUES (:kunde, :betrag, :waehrung, :bezahlt)'
            );
            $stmt->execute([
                'kunde'    => $bestellung->kunde,
                'betrag'   => $bestellung->betrag->cent,
                'waehrung' => $bestellung->betrag->waehrung,
                'bezahlt'  => $bestellung->bezahlt ? 1 : 0,
            ]);

            // Weil Bestellung unveränderlich ist, geben wir ein NEUES Objekt
            // mit gefüllter id zurück, statt das alte zu verändern.
            $neueId = (int) $this->pdo->lastInsertId();

            return new Bestellung(
                $neueId,
                $bestellung->kunde,
                $bestellung->betrag,
                $bestellung->bezahlt,
            );
        }

        // Vorhandene id: aktualisieren.
        $stmt = $this->pdo->prepare(
            'UPDATE bestellung
                SET kunde = :kunde, betrag_cent = :betrag,
                    waehrung = :waehrung, bezahlt = :bezahlt
              WHERE id = :id'
        );
        $stmt->execute([
            'id'       => $bestellung->id,
            'kunde'    => $bestellung->kunde,
            'betrag'   => $bestellung->betrag->cent,
            'waehrung' => $bestellung->betrag->waehrung,
            'bezahlt'  => $bestellung->bezahlt ? 1 : 0,
        ]);

        return $bestellung;
    }

    public function alleOffenen(): array
    {
        // Feste Abfrage ohne Werte von außen - query() genügt.
        $stmt = $this->pdo->query(
            'SELECT id, kunde, betrag_cent, waehrung, bezahlt
               FROM bestellung WHERE bezahlt = 0 ORDER BY id'
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
            (string) $zeile['kunde'],
            new Geld((int) $zeile['betrag_cent'], (string) $zeile['waehrung']),
            (bool) $zeile['bezahlt'],
        );
    }
}
