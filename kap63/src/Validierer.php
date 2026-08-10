<?php

declare(strict_types=1);

namespace App;

use ReflectionAttribute;
use ReflectionClass;

/*
 * Grundstein - Kapitel 63: Reflection und Attribute
 *
 * Der Leser aus dem Kapitel-Bild: Er nimmt ein beliebiges Objekt, schaut
 * per Reflection in seine Klasse, findet an jeder Eigenschaft die
 * notierten Prüf-Attribute und wertet sie aus. Das Ergebnis ist eine
 * Liste von Verstößen - leer, wenn alles in Ordnung ist.
 *
 * Der Validierer kennt weder NichtLeer noch MaxLaenge noch Bereich. Er
 * kennt nur den Vertrag Regel. Neue Regeln kommen hinzu, indem man ein
 * neues Attribut schreibt, das Regel erfüllt - hier ändert sich nichts.
 */
final class Validierer
{
    /**
     * Prüft alle mit Regel-Attributen versehenen Eigenschaften eines
     * Objekts.
     *
     * @return list<Verstoss>
     */
    public function pruefe(object $objekt): array
    {
        $spiegel = new ReflectionClass($objekt);
        $verstoesse = [];

        foreach ($spiegel->getProperties() as $eigenschaft) {
            $wert = $eigenschaft->getValue($objekt);

            // Nur Attribute heraussuchen, die den Vertrag Regel erfüllen -
            // reine Doku-Attribute an derselben Stelle bleiben unberührt.
            $attribute = $eigenschaft->getAttributes(
                Regel::class,
                ReflectionAttribute::IS_INSTANCEOF,
            );

            foreach ($attribute as $attribut) {
                // newInstance() baut aus der Notation ein echtes Regel-Objekt.
                $regel = $attribut->newInstance();

                $meldung = $regel->pruefe($wert);
                if ($meldung !== null) {
                    $verstoesse[] = new Verstoss($eigenschaft->getName(), $meldung);
                }
            }
        }

        return $verstoesse;
    }
}
