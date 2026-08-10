<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 65: Wiederverwendbare, benannte Muster
 *
 * Ein Muster, das man einmal versteht und danach überall anwendet, gehört
 * an eine benannte Stelle - nicht als kryptischer Einzeiler mitten in die
 * Logik. Diese Klasse sammelt ein paar Prüfmuster als sprechende
 * Konstanten. Die komplizierteren nutzen den x-Modus: Er erlaubt Leerraum
 * und "#"-Kommentare IM Muster, ohne dessen Bedeutung zu ändern - so wird
 * ein regulärer Ausdruck lesbar wie normaler Code.
 */
final class Muster
{
    /** Deutsche Postleitzahl: genau fünf Ziffern (ASCII, ohne u-Flag). */
    public const PLZ = '/^\d{5}$/';

    /**
     * Name: Buchstaben inklusive deutscher Umlaute, dazu Leerzeichen und
     * Bindestrich. Das u-Flag ist Pflicht, sonst zählen Umlaute nicht als
     * Buchstabe und die Prüfung urteilt falsch.
     */
    public const NAME = '/^[\p{L} \-]+$/u';

    /**
     * Hex-Farbe wie #a0f oder #aa00ff. Der x-Modus macht die beiden
     * erlaubten Längen sichtbar; das i-Flag lässt Groß- und
     * Kleinschreibung der Hex-Ziffern zu.
     */
    public const HEX_FARBE = '/
        ^\#                # eine Raute zu Beginn (im x-Modus mit \# geschützt)
        (?:
            [0-9a-f]{6}    # entweder sechs Hex-Ziffern (z. B. aa00ff)
            |
            [0-9a-f]{3}    # oder die Kurzform aus drei (z. B. a0f)
        )
        $/xi';

    /**
     * ISO-Datum mit benannten Gruppen, im x-Modus kommentiert. Die
     * Bindestriche zwischen den Gruppen sind wörtlich gemeint.
     */
    public const ISO_DATUM = '/
        ^
        (?<jahr>\d{4})  -   # Jahr, vierstellig
        (?<monat>\d{2}) -   # Monat, zweistellig
        (?<tag>\d{2})       # Tag, zweistellig
        $/x';

    /** Prüft einen Wert gegen ein Muster. true bei genau einem Treffer. */
    public static function passt(string $muster, string $wert): bool
    {
        return preg_match($muster, $wert) === 1;
    }

    /**
     * Zerlegt ein ISO-Datum in seine drei Zahlen. Gibt null zurück, wenn
     * die Eingabe nicht dem Muster entspricht.
     *
     * @return array{jahr: int, monat: int, tag: int}|null
     */
    public static function datumsteile(string $datum): ?array
    {
        if (preg_match(self::ISO_DATUM, $datum, $t) !== 1) {
            return null;
        }

        return [
            'jahr'  => (int) $t['jahr'],
            'monat' => (int) $t['monat'],
            'tag'   => (int) $t['tag'],
        ];
    }
}
