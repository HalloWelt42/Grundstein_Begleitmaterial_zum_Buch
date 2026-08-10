<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Abonnent;
use App\Domain\AbonnentRepository;
use PDO;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Die äußerste Schicht: die Infrastruktur. Dieser Adapter erfüllt den
 * Domänen-Vertrag AbonnentRepository mit echtem SQL über PDO
 * (Kapitel 31 und 32). Er ist der einzige Ort in der Anwendungslogik, an
 * dem fachliches SQL steht - der einmalige Schema-Aufbau bei der
 * Verdrahtung (demo.php) ausgenommen. Die PDO-Verbindung wird ihm über
 * den Konstruktor hereingereicht, statt sie selbst aufzubauen - so weiß
 * er nicht einmal, gegen welche Datenbank er arbeitet.
 */
final class PdoAbonnentRepository implements AbonnentRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    public function existiertMit(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM abonnent WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function speichere(Abonnent $abonnent): Abonnent
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO abonnent (email, angemeldet_am) VALUES (:email, :am)'
        );
        $stmt->execute([
            'email' => $abonnent->email,
            'am'    => $abonnent->angemeldetAm->format('Y-m-d H:i:s'),
        ]);

        // Die Datenbank vergibt die id erst beim Einfügen; weil Abonnent
        // unveränderlich ist, reichen wir sie in einem NEUEN Objekt nach.
        return new Abonnent(
            (int) $this->pdo->lastInsertId(),
            $abonnent->email,
            $abonnent->angemeldetAm,
        );
    }
}
