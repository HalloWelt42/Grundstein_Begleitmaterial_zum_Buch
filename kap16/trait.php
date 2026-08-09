<?php

declare(strict_types=1);

/**
 * Ein Trait bündelt geteiltes Verhalten. Klassen, die miteinander gar
 * nicht verwandt sind, können sich dieselben Methoden und Eigenschaften
 * einverleiben, ohne dafür in eine gemeinsame Vererbungslinie gezwungen
 * zu werden.
 */
trait Protokolliert
{
    /** @var list<string> Gesammelte Protokollzeilen. */
    private array $protokoll = [];

    /**
     * Hängt eine Zeile an das Protokoll an.
     */
    public function notiere(string $eintrag): void
    {
        $this->protokoll[] = $eintrag;
    }

    /**
     * Liefert alle bisher gesammelten Zeilen zurück.
     *
     * @return list<string>
     */
    public function protokoll(): array
    {
        return $this->protokoll;
    }
}

/**
 * Ein zweiter Trait, der eine Erstellungszeit mitbringt. Auch er ist
 * völlig eigenständig und lässt sich frei mit anderen kombinieren.
 */
trait Zeitgestempelt
{
    private ?DateTimeImmutable $erstelltAm = null;

    /**
     * Merkt sich den Zeitpunkt der Erstellung genau ein einziges Mal.
     */
    public function stempele(DateTimeImmutable $zeitpunkt): void
    {
        $this->erstelltAm ??= $zeitpunkt;
    }

    public function erstelltAm(): ?DateTimeImmutable
    {
        return $this->erstelltAm;
    }
}

/**
 * Ein Warenkorb protokolliert, was hineingelegt wird - das eigentliche
 * Protokoll-Verhalten stammt aus dem Trait, nicht aus einer Oberklasse.
 */
final class Warenkorb
{
    use Protokolliert;

    public function lege(string $artikel): void
    {
        $this->notiere("Artikel hinzugefügt: {$artikel}");
    }
}

/**
 * Ein Wartungsauftrag hat mit einem Warenkorb fachlich nichts gemein,
 * teilt sich aber gleich zwei Fähigkeiten über Traits.
 */
final class Wartungsauftrag
{
    use Protokolliert;
    use Zeitgestempelt;

    public function __construct(
        private readonly string $geraet,
    ) {}

    public function starte(DateTimeImmutable $jetzt): void
    {
        $this->stempele($jetzt);
        $this->notiere("Wartung an {$this->geraet} begonnen");
    }
}

$korb = new Warenkorb();
$korb->lege('Schlüssel');
$korb->lege('Fahrradschloss');

echo "Warenkorb-Protokoll:\n";
foreach ($korb->protokoll() as $zeile) {
    echo "  - {$zeile}\n";
}

$auftrag = new Wartungsauftrag('Aufzug');
$auftrag->starte(new DateTimeImmutable('2026-08-09 09:30:00'));
$auftrag->notiere('Sicherung geprüft');

echo "\nWartungsauftrag (erstellt am "
    . $auftrag->erstelltAm()?->format('d.m.Y H:i') . "):\n";
foreach ($auftrag->protokoll() as $zeile) {
    echo "  - {$zeile}\n";
}
