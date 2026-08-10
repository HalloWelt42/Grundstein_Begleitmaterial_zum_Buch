<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang D: Die PSR-Standards
 *
 * Ein selbst definiertes, PSR-3-artiges Logger-Interface und eine
 * konkrete Umsetzung. Der Kern jeder PSR: Man programmiert GEGEN ein
 * Interface, nicht gegen eine bestimmte Klasse. Setzt man statt des
 * SammelLogger eine andere Umsetzung desselben Interface ein, muss am
 * aufrufenden Code keine Zeile geändert werden.
 *
 * Ausgeführt mit PHP 8.4; die Ausgaben stammen aus einem echten Lauf.
 */

// Der Vertrag: was ein Logger können muss. Acht Schweregrade nach
// RFC 5424 plus das allgemeine log(). Genau diese Form hat das echte
// Psr\Log\LoggerInterface - nur mit englischen Methodennamen.
interface LoggerInterface
{
    public function emergency(string $meldung, array $kontext = []): void;
    public function alert(string $meldung, array $kontext = []): void;
    public function critical(string $meldung, array $kontext = []): void;
    public function error(string $meldung, array $kontext = []): void;
    public function warning(string $meldung, array $kontext = []): void;
    public function notice(string $meldung, array $kontext = []): void;
    public function info(string $meldung, array $kontext = []): void;
    public function debug(string $meldung, array $kontext = []): void;

    /** @param array<string, mixed> $kontext */
    public function log(string $stufe, string $meldung, array $kontext = []): void;
}

// Eine konkrete Umsetzung des Vertrags: sie sammelt die Zeilen im
// Speicher. Die acht Stufen-Methoden reichen nur an log() durch - dort
// steckt die eigentliche Arbeit samt Platzhalter-Ersetzung.
final class SammelLogger implements LoggerInterface
{
    /** @var list<string> */
    private array $zeilen = [];

    public function emergency(string $meldung, array $kontext = []): void { $this->log('emergency', $meldung, $kontext); }
    public function alert(string $meldung, array $kontext = []): void     { $this->log('alert', $meldung, $kontext); }
    public function critical(string $meldung, array $kontext = []): void  { $this->log('critical', $meldung, $kontext); }
    public function error(string $meldung, array $kontext = []): void     { $this->log('error', $meldung, $kontext); }
    public function warning(string $meldung, array $kontext = []): void   { $this->log('warning', $meldung, $kontext); }
    public function notice(string $meldung, array $kontext = []): void    { $this->log('notice', $meldung, $kontext); }
    public function info(string $meldung, array $kontext = []): void      { $this->log('info', $meldung, $kontext); }
    public function debug(string $meldung, array $kontext = []): void     { $this->log('debug', $meldung, $kontext); }

    /** @param array<string, mixed> $kontext */
    public function log(string $stufe, string $meldung, array $kontext = []): void
    {
        $this->zeilen[] = strtoupper($stufe) . ': ' . $this->interpoliere($meldung, $kontext);
    }

    /**
     * Ersetzt Platzhalter der Form {name} durch den passenden Wert aus
     * dem Kontext - genau die Interpolation, die PSR-3 vorschreibt.
     *
     * @param array<string, mixed> $kontext
     */
    private function interpoliere(string $meldung, array $kontext): string
    {
        $ersatz = [];
        foreach ($kontext as $schluessel => $wert) {
            $ersatz['{' . $schluessel . '}'] = (string) $wert;
        }

        return strtr($meldung, $ersatz);
    }

    /** @return list<string> */
    public function zeilen(): array
    {
        return $this->zeilen;
    }
}

// Diese Funktion kennt nur das INTERFACE, nie eine konkrete Klasse.
// Sie liefe mit jeder PSR-3-artigen Umsetzung unverändert.
function verarbeiteAnmeldung(LoggerInterface $log, string $benutzer): void
{
    $log->info('Anmeldung von {benutzer}', ['benutzer' => $benutzer]);
    $log->warning('Kontingent zu {prozent}% ausgeschöpft', ['prozent' => 92]);
    $log->error('Zahlung fehlgeschlagen für {benutzer}', ['benutzer' => $benutzer]);
}

$logger = new SammelLogger();
verarbeiteAnmeldung($logger, 'ada');

foreach ($logger->zeilen() as $zeile) {
    echo $zeile . PHP_EOL;
}
