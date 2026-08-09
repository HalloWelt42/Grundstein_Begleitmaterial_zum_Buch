<?php

declare(strict_types=1);

/**
 * Ein Vertrag, den auch Enums erfüllen können: jede Ausprägung liefert
 * eine menschenlesbare Beschriftung.
 */
interface HatBeschriftung
{
    public function beschriftung(): string;
}

/**
 * Int-backed Enum, die zugleich einen Vertrag erfüllt. Die Zahl ist der
 * offizielle Statuscode einer HTTP-Antwort.
 */
enum HttpStatus: int implements HatBeschriftung
{
    case Ok            = 200;
    case Erstellt      = 201;
    case NichtGefunden = 404;
    case ServerFehler  = 500;

    public function beschriftung(): string
    {
        return match ($this) {
            self::Ok            => 'OK',
            self::Erstellt      => 'Created',
            self::NichtGefunden => 'Not Found',
            self::ServerFehler  => 'Internal Server Error',
        };
    }

    /**
     * Eigene Hilfsmethode: alle Codes ab 400 gelten als Fehler.
     */
    public function istFehler(): bool
    {
        return $this->value >= 400;
    }
}

/**
 * Die Funktion kennt nur den Vertrag, nicht die konkrete Enum. Jedes
 * Objekt mit einer beschriftung() passt hinein.
 */
function zeige(HatBeschriftung $b): void
{
    echo $b->beschriftung() . "\n";
}

$antwort = HttpStatus::NichtGefunden;
echo $antwort->value . ' ' . $antwort->beschriftung() . "\n";
var_dump($antwort instanceof HatBeschriftung);
var_dump($antwort->istFehler());

zeige(HttpStatus::Ok);
zeige(HttpStatus::ServerFehler);
