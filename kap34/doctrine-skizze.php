<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 34: Ausblick ORM
 *
 * ACHTUNG: ILLUSTRATION, NICHT LAUFFÄHIG.
 *
 * Diese Datei zeigt, wie derselbe Anwendungsfall aus
 * repository-variante.php mit einem ORM aussähe. Das dafür nötige Paket
 * ist in diesem Buch bewusst NICHT installiert - es geht hier allein um
 * die Einordnung, nicht um eine lauffähige Anleitung. Der Code ist
 * korrektes PHP (php -l meldet keinen Fehler), aber ein Aufruf würde
 * scheitern, weil die verwendeten Klassen und Attribute fehlen.
 *
 * Vergleiche diese Skizze Zeile für Zeile mit der lauffähigen
 * Repository-Variante: Das Buch-Objekt ist dasselbe. Was verschwindet,
 * ist das von Hand geschriebene SQL - das ORM erzeugt es aus den
 * Attributen an der Klasse.
 */

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dieselbe Idee wie das Buch-DTO aus der Repository-Variante - nur wird
 * die Klasse hier zur Entity. Die Attribute beschreiben, wie ihre
 * Eigenschaften auf Tabellenspalten abgebildet werden. Das ORM liest
 * diese Beschreibung und baut daraus das passende SQL selbst.
 */
#[ORM\Entity]
#[ORM\Table(name: 'buch')]
class Buch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $titel;

    #[ORM\Column(type: 'integer')]
    private int $jahr;

    public function __construct(string $titel, int $jahr)
    {
        $this->titel = $titel;
        $this->jahr = $jahr;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function titel(): string
    {
        return $this->titel;
    }

    public function jahr(): int
    {
        return $this->jahr;
    }
}

/*
 * So sähe der Anwendungscode aus. Der EntityManager ist die zentrale
 * Drehscheibe des ORM: persist() merkt ein neues Objekt vor, flush()
 * schreibt alle vorgemerkten Änderungen in einem Rutsch. Ein einziges
 * INSERT haben wir dabei nirgends selbst geschrieben.
 *
 * Wie der $entityManager erzeugt wird, ist hier absichtlich ausgespart -
 * es gehört zur Einrichtung des Pakets, die dieses Buch nicht behandelt.
 */
function beispielablauf(EntityManagerInterface $entityManager): void
{
    // Anlegen: Objekt erzeugen, vormerken, schreiben.
    $buch = new Buch('Grundstein', 2026);
    $entityManager->persist($buch);
    $entityManager->flush();

    // Über das generierte Standard-Repository lesen. Auch hier: kein SQL.
    $repository = $entityManager->getRepository(Buch::class);

    // Ein Buch über seine id - entspricht finde() in der Repository-Variante.
    $eines = $repository->find(1);

    // Einfache Kriterien gibt man als Array an; das erzeugte SQL bleibt
    // verborgen. Für die Bedingung "ab Jahr" oder eigene Sortierungen
    // greift man zum Query Builder der darunterliegenden Ebene (DBAL)
    // oder zur objektnahen Abfragesprache des ORM - hier bleibt es bei
    // der Skizze.
    $alle = $repository->findBy(['jahr' => 2026], ['jahr' => 'ASC']);
}
