<?php

declare(strict_types=1);

/**
 * ACHTUNG: Diese Klasse dient nur der Veranschaulichung. __get, __set und
 * __isset fangen Zugriffe auf NICHT vorhandene Eigenschaften ab und leiten
 * sie in einen internen Behälter um. Das versteckt das Verhalten und macht
 * den Code schwer nachvollziehbar - echte, getippte Eigenschaften oder
 * Property Hooks sind fast immer die bessere Wahl.
 */
final class LockereAblage
{
    /** @var array<string, mixed> */
    private array $daten = [];

    /**
     * Fängt den Lesezugriff auf eine nicht existierende Eigenschaft ab.
     */
    public function __get(string $name): mixed
    {
        echo "  [__get] '{$name}' gelesen\n";

        return $this->daten[$name] ?? null;
    }

    /**
     * Fängt die Zuweisung an eine nicht existierende Eigenschaft ab.
     */
    public function __set(string $name, mixed $wert): void
    {
        echo "  [__set] '{$name}' gesetzt\n";
        $this->daten[$name] = $wert;
    }

    /**
     * Beantwortet isset()/empty() für die abgefangenen Eigenschaften.
     */
    public function __isset(string $name): bool
    {
        return isset($this->daten[$name]);
    }
}

$ablage = new LockereAblage();

$ablage->titel = 'Grundstein';   // ruft __set auf
echo $ablage->titel . "\n";      // ruft __get auf

var_dump(isset($ablage->titel)); // ruft __isset auf
var_dump(isset($ablage->fehlt)); // ebenfalls __isset
