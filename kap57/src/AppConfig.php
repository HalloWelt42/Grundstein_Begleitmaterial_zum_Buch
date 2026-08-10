<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 57: Konfiguration, Umgebungen und Secrets
 *
 * Das typisierte Konfigurationsobjekt. Statt roher Zeichenketten quer durch die
 * Anwendung zu reichen, wird die Konfiguration EINMAL eingelesen, im
 * Konstruktor geprüft und danach unveränderlich gehalten - genau wie ein
 * Wertobjekt aus Kapitel 54. Wer ein AppConfig in der Hand hält, hält
 * garantiert eine gültige Konfiguration: gültige Umgebung, gültige
 * Absenderadresse, nicht leere Datenbank-Adresse, in der Produktion keine
 * offene Fehlerausgabe.
 *
 * Das Geheimnis (apiSchluessel) steckt in einem Secret-Wertobjekt und kann so
 * nicht versehentlich in Logs oder Ausgaben geraten.
 */
final readonly class AppConfig
{
    public function __construct(
        public Umgebung $umgebung,
        public bool $debug,
        public string $dbDsn,
        public string $mailFrom,
        public Secret $apiSchluessel,
    ) {
        // Invariante 1: In der Produktion darf die Fehlerausgabe nie an sein -
        // sie würde interne Details und womöglich Geheimnisse verraten.
        if ($umgebung === Umgebung::Produktion && $debug === true) {
            throw new ConfigFehler(
                'In der Produktion muss die Fehlerausgabe (APP_DEBUG) ausgeschaltet sein.'
            );
        }

        // Invariante 2: Die Absenderadresse muss syntaktisch gültig sein.
        if (filter_var($mailFrom, FILTER_VALIDATE_EMAIL) === false) {
            throw new ConfigFehler("Ungültige Absenderadresse MAIL_FROM: '{$mailFrom}'.");
        }

        // Invariante 3: Ohne Datenbank-Adresse läuft nichts.
        if ($dbDsn === '') {
            throw new ConfigFehler('Die Datenbank-Adresse DB_DSN darf nicht leer sein.');
        }
    }

    /**
     * Baut das Konfigurationsobjekt aus den rohen Umgebungswerten. Hier - und
     * nur hier - werden die Zeichenketten in feste Typen übersetzt und geprüft.
     */
    public static function ausEnv(Env $env): self
    {
        $envName  = $env->hole('APP_ENV') ?? 'dev';
        $umgebung = Umgebung::tryFrom($envName)
            ?? throw new ConfigFehler("Unbekannte Umgebung APP_ENV: '{$envName}'.");

        return new self(
            umgebung: $umgebung,
            debug: $env->bool('APP_DEBUG', false),
            dbDsn: $env->pflicht('DB_DSN'),
            mailFrom: $env->pflicht('MAIL_FROM'),
            apiSchluessel: new Secret($env->pflicht('API_KEY')),
        );
    }

    /**
     * Eine gefahrlose Darstellung für Logs und Diagnose: Das Geheimnis wird
     * maskiert, alle anderen Werte erscheinen im Klartext. Diese Methode ist
     * der einzige empfohlene Weg, die Konfiguration auszugeben.
     *
     * @return array<string, string>
     */
    public function alsAnzeige(): array
    {
        return [
            'umgebung'      => $this->umgebung->value,
            'debug'         => $this->debug ? 'an' : 'aus',
            'dbDsn'         => $this->dbDsn,
            'mailFrom'      => $this->mailFrom,
            'apiSchluessel' => (string) $this->apiSchluessel, // Secret -> ***
        ];
    }
}
