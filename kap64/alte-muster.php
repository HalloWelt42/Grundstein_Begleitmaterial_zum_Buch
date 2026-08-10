<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

// So löste man verzögertes Bauen VOR PHP 8.4: von Hand. Beide Wege
// funktionieren, aber beide haben einen Preis, den Lazy Objects nicht haben.

interface Rechner
{
    public function quadrat(int $n): int;
}

final class EchterRechner implements Rechner
{
    public static int $bauten = 0;

    public function __construct()
    {
        // Stellvertreter für einen teuren Aufbau.
        self::$bauten++;
    }

    public function quadrat(int $n): int
    {
        return $n * $n;
    }
}

/**
 * Weg 1: ein handgeschriebener Stellvertreter. Er muss JEDE Methode des
 * Vertrags selbst nachbilden und einzeln durchreichen - hier nur eine,
 * bei einem echten Dienst schnell ein Dutzend.
 */
final class FaulerRechner implements Rechner
{
    private ?Rechner $echt = null;

    /** @param callable(): Rechner $fabrik */
    public function __construct(private $fabrik) {}

    public function quadrat(int $n): int
    {
        $this->echt ??= ($this->fabrik)();

        return $this->echt->quadrat($n);
    }
}

EchterRechner::$bauten = 0;
$faul = new FaulerRechner(static fn (): Rechner => new EchterRechner());
echo 'Weg 1 vor Zugriff:  ' . EchterRechner::$bauten . " gebaut\n";
echo 'Weg 1 quadrat(7):   ' . $faul->quadrat(7)
    . ' (' . EchterRechner::$bauten . " gebaut)\n";

/**
 * Weg 2: die __get-Trickserei. Eine Hülle fängt jeden Eigenschaftszugriff
 * ab und baut beim ersten das echte Objekt. Der Haken: Der Zugriff geht nur
 * über __get - Methoden, Typprüfung und statische Analyse fallen durch.
 */
final class Datenblatt
{
    public function __construct(public readonly string $titel) {}
}

final class FauleHuelle
{
    private ?object $echt = null;

    /** @param callable(): object $fabrik */
    public function __construct(private $fabrik) {}

    public function __get(string $name): mixed
    {
        $this->echt ??= ($this->fabrik)();

        return $this->echt->$name;
    }
}

$huelle = new FauleHuelle(static fn (): Datenblatt => new Datenblatt('Quartalsbericht'));
echo 'Weg 2 Titel:        ' . $huelle->titel . "\n";
echo 'Weg 2 ist Datenblatt? '
    . ($huelle instanceof Datenblatt ? 'ja' : 'nein') . "\n";
