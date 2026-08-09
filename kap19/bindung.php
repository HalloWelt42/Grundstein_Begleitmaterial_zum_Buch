<?php

declare(strict_types=1);

/**
 * Basisklasse mit zwei Erzeuger-Methoden, die sich nur in einem Wort
 * unterscheiden: self gegen static.
 */
class Modell
{
    /**
     * self:: bindet fest an die Klasse, in der diese Zeile STEHT -
     * hier also immer an Modell.
     */
    public static function mitSelf(): Modell
    {
        return new self();
    }

    /**
     * static:: bindet an die zur Laufzeit tatsächlich aufgerufene Klasse.
     * Das nennt man späte statische Bindung.
     */
    public static function mitStatic(): Modell
    {
        return new static();
    }
}

final class Kompaktwagen extends Modell
{
}

// Beide Methoden werden über die Unterklasse aufgerufen.
$ausSelf = Kompaktwagen::mitSelf();
$ausStatic = Kompaktwagen::mitStatic();

echo 'mitSelf liefert:   ' . $ausSelf::class . "\n";
echo 'mitStatic liefert: ' . $ausStatic::class . "\n";
