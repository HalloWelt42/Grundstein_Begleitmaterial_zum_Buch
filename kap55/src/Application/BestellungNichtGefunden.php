<?php

declare(strict_types=1);

namespace App\Application;

use RuntimeException;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Eine fachliche Ausnahme des Kerns. Sie gehört bewusst zur Anwendung, nicht
 * zu einem Adapter: Ob "nicht gefunden" später zu einem 404 wird oder zu einer
 * Fehlerzeile auf der Kommandozeile, entscheidet erst der treibende Adapter.
 * Der Kern kennt nur den fachlichen Sachverhalt.
 */
final class BestellungNichtGefunden extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Keine Bestellung mit der id {$id} gefunden.");
    }
}
