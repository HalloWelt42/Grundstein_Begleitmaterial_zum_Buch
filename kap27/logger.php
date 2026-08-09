<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 27: Logging
 *
 * Ein winziger, PSR-3-naher Logger zum Verstehen des Standards. Er kennt
 * dieselben acht Stufen wie PSR-3 und dieselbe Signatur für log() samt
 * Platzhalter-Interpolation. Damit jeder Dienst gegen die Schnittstelle
 * statt gegen die konkrete Klasse programmieren kann, definieren wir ein
 * eigenes LoggerInterface und lassen unseren Logger es erfüllen.
 *
 * In einem echten Projekt würdest du keinen Logger selbst schreiben,
 * sondern eine erprobte Bibliothek einbinden. Viele PSR-3-Logger erben
 * dabei von Psr\Log\AbstractLogger, das die acht Kurzmethoden schon
 * mitbringt - diese Fassung dient allein dem Verständnis.
 *
 * Alle Ausgaben im Buch stammen aus einem echten Lauf mit PHP 8.4.
 */

namespace App\Log;

use InvalidArgumentException;
use Stringable;

/**
 * Unser eigener Logger-Vertrag, der die Gestalt von PSR-3 aufgreift: acht
 * Kurzmethoden für die acht Stufen plus die allgemeine log()-Methode, auf
 * die alle hinauslaufen. Wer nur diesen Vertrag kennt, bleibt unabhängig
 * davon, welcher konkrete Logger dahintersteckt.
 */
interface LoggerInterface
{
    /** @param array<string, mixed> $kontext */
    public function emergency(string|Stringable $nachricht, array $kontext = []): void;

    /** @param array<string, mixed> $kontext */
    public function alert(string|Stringable $nachricht, array $kontext = []): void;

    /** @param array<string, mixed> $kontext */
    public function critical(string|Stringable $nachricht, array $kontext = []): void;

    /** @param array<string, mixed> $kontext */
    public function error(string|Stringable $nachricht, array $kontext = []): void;

    /** @param array<string, mixed> $kontext */
    public function warning(string|Stringable $nachricht, array $kontext = []): void;

    /** @param array<string, mixed> $kontext */
    public function notice(string|Stringable $nachricht, array $kontext = []): void;

    /** @param array<string, mixed> $kontext */
    public function info(string|Stringable $nachricht, array $kontext = []): void;

    /** @param array<string, mixed> $kontext */
    public function debug(string|Stringable $nachricht, array $kontext = []): void;

    /** @param array<string, mixed> $kontext */
    public function log(string $stufe, string|Stringable $nachricht, array $kontext = []): void;
}

/**
 * Ein einfacher, PSR-3-naher Logger, der Zeilen auf einen Stream schreibt.
 */
final class EinfacherLogger implements LoggerInterface
{
    /**
     * Die acht Stufen nach PSR-3, von der dringendsten zur harmlosesten.
     * Der Zahlenwert dient nur dem Vergleich: kleiner heißt dringender.
     *
     * @var array<string, int>
     */
    private const STUFEN = [
        'emergency' => 0,
        'alert'     => 1,
        'critical'  => 2,
        'error'     => 3,
        'warning'   => 4,
        'notice'    => 5,
        'info'      => 6,
        'debug'     => 7,
    ];

    /**
     * @param resource $ziel         Offener Stream, auf den geschrieben wird.
     * @param string   $mindeststufe Ab dieser Stufe wird protokolliert; alles
     *                               Harmlosere wird verworfen.
     */
    public function __construct(
        private $ziel,
        private readonly string $mindeststufe = 'debug',
    ) {}

    /** @param array<string, mixed> $kontext */
    public function emergency(string|Stringable $nachricht, array $kontext = []): void
    {
        $this->log('emergency', $nachricht, $kontext);
    }

    /** @param array<string, mixed> $kontext */
    public function alert(string|Stringable $nachricht, array $kontext = []): void
    {
        $this->log('alert', $nachricht, $kontext);
    }

    /** @param array<string, mixed> $kontext */
    public function critical(string|Stringable $nachricht, array $kontext = []): void
    {
        $this->log('critical', $nachricht, $kontext);
    }

    /** @param array<string, mixed> $kontext */
    public function error(string|Stringable $nachricht, array $kontext = []): void
    {
        $this->log('error', $nachricht, $kontext);
    }

    /** @param array<string, mixed> $kontext */
    public function warning(string|Stringable $nachricht, array $kontext = []): void
    {
        $this->log('warning', $nachricht, $kontext);
    }

    /** @param array<string, mixed> $kontext */
    public function notice(string|Stringable $nachricht, array $kontext = []): void
    {
        $this->log('notice', $nachricht, $kontext);
    }

    /** @param array<string, mixed> $kontext */
    public function info(string|Stringable $nachricht, array $kontext = []): void
    {
        $this->log('info', $nachricht, $kontext);
    }

    /** @param array<string, mixed> $kontext */
    public function debug(string|Stringable $nachricht, array $kontext = []): void
    {
        $this->log('debug', $nachricht, $kontext);
    }

    /**
     * Das Herzstück: schreibt eine Zeile, sofern die Stufe wichtig genug ist.
     * Genau diese Signatur schreibt auch PSR-3 vor - alle acht Kurzmethoden
     * oben reichen nur ihre Stufe an diese eine Methode weiter.
     *
     * @param array<string, mixed> $kontext
     */
    public function log(string $stufe, string|Stringable $nachricht, array $kontext = []): void
    {
        if (!isset(self::STUFEN[$stufe])) {
            throw new InvalidArgumentException("Unbekannte Log-Stufe: {$stufe}");
        }

        // Nur schreiben, wenn die Stufe mindestens so dringend wie die Schwelle ist.
        if (self::STUFEN[$stufe] > self::STUFEN[$this->mindeststufe]) {
            return;
        }

        $text  = $this->platzhalterFuellen((string) $nachricht, $kontext);
        $zeit  = date('Y-m-d H:i:s');
        $marke = strtoupper($stufe);

        fwrite($this->ziel, "[{$zeit}] {$marke}: {$text}\n");
    }

    /**
     * Ersetzt {schluessel} in der Nachricht durch Werte aus dem Kontext.
     * PSR-3 legt das Platzhalter-Format fest; das Einsetzen selbst ist nur
     * empfohlen. Wir setzen es um: nur in Text wandelbare Werte werden
     * eingesetzt, der Rest bleibt unangetastet stehen.
     *
     * @param array<string, mixed> $kontext
     */
    private function platzhalterFuellen(string $nachricht, array $kontext): string
    {
        $ersetzungen = [];
        foreach ($kontext as $schluessel => $wert) {
            if (is_scalar($wert) || $wert instanceof Stringable) {
                $ersetzungen['{' . $schluessel . '}'] = (string) $wert;
            }
        }

        return strtr($nachricht, $ersetzungen);
    }
}
