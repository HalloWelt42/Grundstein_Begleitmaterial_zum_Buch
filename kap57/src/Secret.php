<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Ein Wertobjekt (Kapitel 54) für ein Geheimnis - einen API-Schlüssel, ein
 * Passwort, ein Token. Sein Zweck ist Disziplin: Der Klartext bleibt gekapselt
 * und lässt sich nur über die bewusst umständlich benannte Methode offenbare()
 * lesen. Die üblichen versehentlichen Wege nach draußen - echo,
 * String-Interpolation, var_dump(), print_r() - zeigen nur eine Maske.
 *
 * Das ist eine Disziplin-Schranke, keine undurchdringliche Mauer: var_export(),
 * serialize() und der Zugriff per Reflection erreichen das gekapselte Feld
 * weiterhin im Klartext. Secret macht das versehentliche Leck praktisch
 * unmöglich und den absichtlichen Zugriff sichtbar - mehr soll es nicht leisten.
 */
final readonly class Secret
{
    public function __construct(private string $wert)
    {
        if ($wert === '') {
            throw new ConfigFehler('Ein Geheimnis darf nicht leer sein.');
        }
    }

    // Der einzige Weg an den Klartext - absichtlich sperrig benannt, damit ein
    // Zugriff im Code auffällt und begründet werden muss.
    public function offenbare(): string
    {
        return $this->wert;
    }

    // Fängt echo und String-Interpolation ab: "{$secret}" wird zu "***".
    public function __toString(): string
    {
        return '***';
    }

    // Fängt var_dump() und print_r() ab (beide achten auf __debugInfo).
    public function __debugInfo(): array
    {
        return ['wert' => '***'];
    }
}
