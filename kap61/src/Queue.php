<?php

declare(strict_types=1);

namespace App;

use PDO;
use Throwable;

/**
 * Eine schlichte, datenbankgestützte Auftragswarteschlange.
 *
 * Die ganze Queue ruht auf einer einzigen Tabelle "auftrag". Der Erzeuger
 * stellt Aufträge mit lege() ein; ein Worker greift den ältesten offenen
 * Auftrag mit reserviere() heraus und meldet danach erledige() oder
 * meldeFehler(). Das Herausgreifen läuft in einer Transaktion, sodass
 * kein Auftrag doppelt bearbeitet wird.
 */
final class Queue
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    /**
     * Legt die Tabelle an, falls sie noch nicht existiert.
     */
    public function migriere(): void
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS auftrag (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                typ             TEXT    NOT NULL,
                daten           TEXT    NOT NULL,
                status          TEXT    NOT NULL DEFAULT 'offen',
                versuche        INTEGER NOT NULL DEFAULT 0,
                max_versuche    INTEGER NOT NULL DEFAULT 3,
                fehler          TEXT,
                erstellt_am     TEXT    NOT NULL,
                aktualisiert_am TEXT    NOT NULL
            )"
        );
    }

    /**
     * Stellt einen neuen Auftrag ein und liefert seine Kennung.
     *
     * Genau das ruft der Web-Prozess auf, bevor er dem Nutzer sofort
     * antwortet: Nutzlast nach JSON kodieren, eine Zeile mit Status
     * "offen" schreiben - fertig.
     *
     * @param array<string, mixed> $daten Nutzlast des Auftrags.
     */
    public function lege(string $typ, array $daten, int $maxVersuche = 3): int
    {
        $einfuegen = $this->pdo->prepare(
            "INSERT INTO auftrag (typ, daten, status, max_versuche, erstellt_am, aktualisiert_am)
             VALUES (:typ, :daten, 'offen', :max, :jetzt, :jetzt)"
        );
        $einfuegen->execute([
            'typ'   => $typ,
            'daten' => $this->kodiere($daten),
            'max'   => $maxVersuche,
            'jetzt' => $this->jetzt(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Greift den ältesten offenen Auftrag heraus und beansprucht ihn.
     *
     * Der Kern der ganzen Queue. Das Herausgreifen läuft in einer
     * Transaktion: erst den ältesten offenen Auftrag suchen, dann ihn
     * mit einem UPDATE beanspruchen - aber nur, solange er noch "offen"
     * ist. Diese Bedingung teilt einen Auftrag genau einem Worker zu.
     *
     * @return Auftrag|null Der reservierte Auftrag oder null, wenn nichts
     *                      (mehr) offen ist.
     */
    public function reserviere(): ?Auftrag
    {
        // "BEGIN IMMEDIATE" holt die Schreibsperre sofort - nicht erst beim
        // ersten Schreibbefehl. Ohne das könnten zwei Worker beide lesen
        // und dann beim Hochstufen zur Schreibsperre gegenseitig blockieren
        // ("database is locked"). So wartet der zweite Worker dank des
        // busy_timeout aus Datenbank::oeffne, bis der erste fertig ist.
        $this->pdo->exec('BEGIN IMMEDIATE');

        try {
            // Den ältesten offenen Auftrag heraussuchen.
            $suche = $this->pdo->query(
                "SELECT id, typ, daten, versuche FROM auftrag
                 WHERE status = 'offen' ORDER BY id LIMIT 1"
            );
            $zeile = $suche->fetch();

            if ($zeile === false) {
                $this->pdo->exec('COMMIT');

                return null; // Warteschlange ist leer.
            }

            // Genau diese Zeile beanspruchen - aber nur, wenn sie noch offen
            // ist. Diese Bedingung ist die eigentliche Sperre gegen Doppellauf.
            $beanspruchen = $this->pdo->prepare(
                "UPDATE auftrag
                    SET status = 'in_arbeit',
                        versuche = versuche + 1,
                        aktualisiert_am = :jetzt
                  WHERE id = :id AND status = 'offen'"
            );
            $beanspruchen->execute(['jetzt' => $this->jetzt(), 'id' => $zeile['id']]);

            if ($beanspruchen->rowCount() !== 1) {
                // Ein anderer Worker war schneller - diesmal leer ausgehen.
                $this->pdo->exec('COMMIT');

                return null;
            }

            $this->pdo->exec('COMMIT');
        } catch (Throwable $fehler) {
            $this->pdo->exec('ROLLBACK');

            throw $fehler;
        }

        return new Auftrag(
            (int) $zeile['id'],
            (string) $zeile['typ'],
            $this->dekodiere((string) $zeile['daten']),
            (int) $zeile['versuche'] + 1, // Wert nach dem Hochzählen.
        );
    }

    /**
     * Markiert einen Auftrag als erfolgreich erledigt.
     */
    public function erledige(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE auftrag
                SET status = 'erledigt', aktualisiert_am = :jetzt
              WHERE id = :id"
        );
        $stmt->execute(['jetzt' => $this->jetzt(), 'id' => $id]);
    }

    /**
     * Meldet einen Fehlschlag: erneut versuchen oder endgültig aufgeben.
     *
     * Weil reserviere() den Versuchszähler schon beim Herausgreifen
     * erhöht hat, steht hier bereits die richtige Zahl. Ist sie kleiner
     * als die Grenze, wandert der Auftrag zurück auf "offen"; ist die
     * Grenze erreicht, gilt er als "fehlgeschlagen" (toter Auftrag).
     */
    public function meldeFehler(int $id, string $grund): void
    {
        $laden = $this->pdo->prepare(
            'SELECT versuche, max_versuche FROM auftrag WHERE id = :id'
        );
        $laden->execute(['id' => $id]);
        $zeile = $laden->fetch();

        if ($zeile === false) {
            return; // Auftrag gibt es nicht (mehr).
        }

        $erschoepft = (int) $zeile['versuche'] >= (int) $zeile['max_versuche'];
        $neuerStatus = $erschoepft ? 'fehlgeschlagen' : 'offen';

        $stmt = $this->pdo->prepare(
            "UPDATE auftrag
                SET status = :status, fehler = :fehler, aktualisiert_am = :jetzt
              WHERE id = :id"
        );
        $stmt->execute([
            'status' => $neuerStatus,
            'fehler' => $grund,
            'jetzt'  => $this->jetzt(),
            'id'     => $id,
        ]);
    }

    /**
     * Zählt die Aufträge in einem bestimmten Status.
     */
    public function zaehle(string $status): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM auftrag WHERE status = :status'
        );
        $stmt->execute(['status' => $status]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Kodiert die Nutzlast nach JSON und wirft bei nicht serialisierbaren
     * Werten eine Ausnahme statt eines stillen false (siehe Kapitel 29).
     *
     * @param array<string, mixed> $daten
     */
    private function kodiere(array $daten): string
    {
        return json_encode($daten, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Entpackt die JSON-Nutzlast wieder in ein Array.
     *
     * @return array<string, mixed>
     */
    private function dekodiere(string $json): array
    {
        /** @var array<string, mixed> $daten */
        $daten = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return $daten;
    }

    /**
     * Einheitlicher Zeitstempel für die Tabelle.
     */
    private function jetzt(): string
    {
        return date('Y-m-d H:i:s');
    }
}
