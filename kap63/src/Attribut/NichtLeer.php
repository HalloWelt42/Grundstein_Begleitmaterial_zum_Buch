<?php

declare(strict_types=1);

namespace App\Attribut;

use App\Regel;
use Attribute;

/*
 * Grundstein - Kapitel 63: Reflection und Attribute
 *
 * Ein eigenes Attribut. Das #[Attribute(...)] darüber macht aus dieser
 * gewöhnlichen Klasse ein Attribut, das nur an Eigenschaften notiert
 * werden darf (TARGET_PROPERTY). Weil die Klasse zugleich Regel erfüllt,
 * trägt das Attribut sein Prüfverhalten selbst in sich.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class NichtLeer implements Regel
{
    public function pruefe(mixed $wert): ?string
    {
        // Nicht gesetzt oder eine Zeichenkette aus nur Leerraum gilt als leer.
        if ($wert === null || (is_string($wert) && trim($wert) === '')) {
            return 'darf nicht leer sein';
        }

        return null;
    }
}
