<?php

declare(strict_types=1);

/*
 * Grundstein - Beispiel zu Kapitel 1: "So sieht modernes PHP aus".
 *
 * Dieses Skript ist ein Ausblick, kein Lehrstoff - du musst hier noch
 * nichts verstehen. Es zeigt an einem winzigen Beispiel, wohin die Reise
 * geht: klare Typen, sprechende Enums und Objekte, die ihre Daten schützen.
 * Lauffähig ab PHP 8.4.
 */

// Ein Enum macht aus einem "magischen String" einen festen, prüfbaren Typ.
enum Rolle: string
{
    case Leser = 'leser';
    case Autor = 'autor';
    case Admin = 'admin';

    // Enums dürfen Methoden haben - hier eine menschenlesbare Bezeichnung.
    public function anzeigename(): string
    {
        return match ($this) {
            Rolle::Leser => 'Leserin oder Leser',
            Rolle::Autor => 'Autorin oder Autor',
            Rolle::Admin => 'Administration',
        };
    }
}

final class Benutzer
{
    // Property Hook (PHP 8.4): "vollerName" ist ein berechnetes, nur lesbares
    // Feld. Es speichert nichts, sondern leitet seinen Wert bei jedem Zugriff ab.
    public string $vollerName {
        get => trim("{$this->vorname} {$this->nachname}");
    }

    public function __construct(
        // Konstruktor-Promotion: Parameter werden direkt zu Objektfeldern.
        // "private(set)" = von außen lesbar, aber nur intern änderbar
        // (asymmetrische Sichtbarkeit, neu in PHP 8.4).
        public private(set) string $vorname,
        public private(set) string $nachname,
        public Rolle $rolle = Rolle::Leser,
    ) {}
}

$benutzer = new Benutzer('Ada', 'Lovelace', Rolle::Autor);

printf(
    '%s ist als "%s" angemeldet.%s',
    $benutzer->vollerName,
    $benutzer->rolle->anzeigename(),
    PHP_EOL,
);
