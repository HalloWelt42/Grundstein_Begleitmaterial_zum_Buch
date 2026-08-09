<?php

declare(strict_types=1);

/*
 * Property Hooks im Zusammenspiel mit Sichtbarkeit.
 *
 * Ein Hook regelt, was bei einem Zugriff passiert; die Sichtbarkeit
 * regelt, wer zugreifen darf. Die asymmetrische Sichtbarkeit
 * "public private(set)" macht eine Eigenschaft von außen lesbar, aber
 * nur innerhalb der Klasse schreibbar - und der set-Hook formt den Wert,
 * sobald intern geschrieben wird.
 */

/**
 * Ein Artikel, dessen Titel beim Setzen zu einem sauberen URL-Baustein
 * (Slug) normalisiert wird. Von außen ist der Slug nur lesbar.
 */
final class Artikel
{
    // Von außen lesbar, nur innerhalb der Klasse schreibbar. Der set-Hook
    // normalisiert den Titel zu einem sauberen URL-Baustein (Slug).
    public private(set) string $slug {
        set (string $value) {
            $this->slug = strtolower(str_replace(' ', '-', trim($value)));
        }
    }

    public function __construct(string $titel)
    {
        $this->slug = $titel;
    }
}

$artikel = new Artikel('  Moderne PHP Klassen  ');
echo "Slug: {$artikel->slug}\n";

// Ein Schreibversuch von außen ist verboten - die Sichtbarkeit greift,
// bevor der Hook überhaupt zum Zuge kommt.
try {
    $artikel->slug = 'von-aussen';
} catch (Error $e) {
    echo "Abgewiesen: {$e->getMessage()}\n";
}
