<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Ein Vertrag für die Erzeugung eines Bestätigungscodes. In Produktion
 * steckt dahinter echter Zufall (etwa bin2hex(random_bytes(...))); im
 * Test ersetzen wir ihn durch einen Generator, der einen festen,
 * vorhersagbaren Wert liefert. Zufall und Test vertragen sich nicht.
 */
interface TokenGenerator
{
    public function erzeuge(): string;
}
