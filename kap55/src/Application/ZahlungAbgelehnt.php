<?php

declare(strict_types=1);

namespace App\Application;

use RuntimeException;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Die fachliche Ausnahme für eine gescheiterte Zahlung. Sie ist Teil des
 * Vertrags ZahlungsPort: Jeder Zahlungs-Adapter wirft sie, wenn der Anbieter
 * die Belastung nicht ausführt. Der Kern reicht sie unverändert nach oben; der
 * treibende Adapter übersetzt sie später in seine eigene Sprache (etwa 402).
 */
final class ZahlungAbgelehnt extends RuntimeException
{
}
