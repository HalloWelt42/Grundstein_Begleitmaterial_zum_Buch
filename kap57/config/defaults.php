<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Die Standardwerte der Konfiguration - die schwächste der drei Quellen.
 * Hier stehen ausschließlich unkritische Vorgaben für die lokale
 * Entwicklung, die gefahrlos im Repository liegen dürfen.
 *
 * WICHTIG: Niemals ein Geheimnis (Passwort, API-Schlüssel, Token) in diese
 * Datei schreiben. Secrets kommen aus der .env-Datei oder aus echten
 * Umgebungsvariablen und überschreiben die Standardwerte.
 *
 * @return array<string, string>
 */

return [
    'APP_ENV'   => 'dev',
    'APP_DEBUG' => 'true',
    'DB_DSN'    => 'sqlite::memory:',
    'MAIL_FROM' => 'noreply@example.test',
    // Kein API_KEY: ein Geheimnis gehört nicht in eingecheckten Code.
];
