<?php

declare(strict_types=1);

/**
 * Sammelt HTTP-Statuscodes als benannte Konstanten. Konstanten gehören
 * zur Klasse und ändern sich nie - ideal für feste Kennzahlen.
 */
final class HttpStatus
{
    // Öffentliche Konstanten: von überall über HttpStatus::OK lesbar.
    // Der Typ int steht seit PHP 8.3 vor dem Namen.
    public const int OK = 200;
    public const int NICHT_GEFUNDEN = 404;

    // Interne Konstante: nur innerhalb dieser Klasse sichtbar.
    private const array TEXTE = [
        self::OK => 'OK',
        self::NICHT_GEFUNDEN => 'Not Found',
    ];

    /**
     * Liefert den Klartext zu einem Statuscode.
     */
    public static function text(int $code): string
    {
        return self::TEXTE[$code] ?? 'Unbekannt';
    }
}

// Eine öffentliche Konstante liest man am Klassennamen ab.
echo HttpStatus::OK . "\n";

// Die Zuordnung läuft über eine Methode, weil die Tabelle privat ist.
echo HttpStatus::text(HttpStatus::NICHT_GEFUNDEN) . "\n";
echo HttpStatus::text(500) . "\n";
