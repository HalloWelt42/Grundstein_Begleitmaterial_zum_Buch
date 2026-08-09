<?php

declare(strict_types=1);

namespace App;

use PDO;

/*
 * Grundstein - Kapitel 32: Datenzugriff kapseln
 *
 * Die eine Stelle, an der SQL für Kunden lebt. Diese Klasse erfüllt den
 * Vertrag KundenRepository mit PDO. Alles Datenbankspezifische - die
 * Abfragen, die Prepared Statements, das Übersetzen einer Ergebniszeile
 * in ein Kunde-Objekt - ist hier gebündelt und nirgends sonst.
 *
 * Wer Kunden braucht, spricht mit dem Interface. Wer wissen will, wie
 * Kunden gespeichert werden, schaut in genau diese Datei.
 */
final class PdoKundenRepository implements KundenRepository
{
    // Die Verbindung wird von außen hineingereicht (Komposition aus
    // Kapitel 15), nicht in der Klasse selbst aufgebaut. So bleibt das
    // Repository frei von der Frage, WELCHE Datenbank dahintersteht.
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    public function find(int $id): ?Kunde
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, umsatz_cent FROM kunde WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        $zeile = $stmt->fetch();

        // Kein Treffer? Dann null - nicht false und kein leeres Array.
        return $zeile === false ? null : $this->aufKunde($zeile);
    }

    public function findAll(): array
    {
        // Feste Abfrage ohne Werte von außen - query() genügt.
        $stmt = $this->pdo->query(
            'SELECT id, name, email, umsatz_cent FROM kunde ORDER BY name'
        );

        // Jede Zeile wandert durch dieselbe Mapping-Methode. Die
        // First-Class-Callable-Schreibweise aufKunde(...) übergibt die
        // Methode als aufrufbaren Wert an array_map().
        return array_map($this->aufKunde(...), $stmt->fetchAll());
    }

    public function save(Kunde $kunde): Kunde
    {
        // Noch keine id: einfügen und die vergebene id nachreichen.
        if ($kunde->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO kunde (name, email, umsatz_cent)
                 VALUES (:name, :email, :umsatz)'
            );
            $stmt->execute([
                'name'   => $kunde->name,
                'email'  => $kunde->email,
                'umsatz' => $kunde->umsatzCent,
            ]);

            // Weil Kunde unveränderlich ist, geben wir ein NEUES Objekt
            // mit gefüllter id zurück, statt das alte zu verändern.
            $neueId = (int) $this->pdo->lastInsertId();

            return new Kunde($neueId, $kunde->name, $kunde->email, $kunde->umsatzCent);
        }

        // Vorhandene id: aktualisieren.
        $stmt = $this->pdo->prepare(
            'UPDATE kunde
                SET name = :name, email = :email, umsatz_cent = :umsatz
              WHERE id = :id'
        );
        $stmt->execute([
            'id'     => $kunde->id,
            'name'   => $kunde->name,
            'email'  => $kunde->email,
            'umsatz' => $kunde->umsatzCent,
        ]);

        return $kunde;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM kunde WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Das Herzstück der Kapselung: die Übersetzung einer rohen
     * Ergebniszeile in ein getipptes Kunde-Objekt. Diese Zuordnung steht
     * genau einmal im ganzen Programm. Ändert sich das Schema, ist hier
     * die einzige Stelle, die mitwandert.
     *
     * @param array<string, mixed> $zeile
     */
    private function aufKunde(array $zeile): Kunde
    {
        return new Kunde(
            (int) $zeile['id'],
            (string) $zeile['name'],
            (string) $zeile['email'],
            (int) $zeile['umsatz_cent'],
        );
    }
}
