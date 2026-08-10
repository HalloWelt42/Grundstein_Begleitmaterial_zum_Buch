<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 63: Reflection und Attribute
 *
 * Der gemeinsame Vertrag aller Prüfregeln. Jedes Prüf-Attribut
 * (NichtLeer, MaxLaenge, Bereich ...) ist zugleich eine Regel: Es weiß,
 * wie es einen einzelnen Wert prüft. Genau dadurch kann der Validierer
 * jedes gefundene Attribut behandeln, ohne seinen konkreten Typ zu
 * kennen - er kennt nur diesen Vertrag.
 */
interface Regel
{
    /**
     * Prüft einen einzelnen Wert. Liefert null, wenn er in Ordnung ist,
     * sonst eine kurze Fehlermeldung.
     */
    public function pruefe(mixed $wert): ?string;
}
