<?php

declare(strict_types=1);

namespace App;

/*
 * Absichtlich im alten Stil geschrieben, damit Rector zeigen kann,
 * was es modernisiert. Nach "vendor/bin/rector process" entspricht
 * diese Datei dem Inhalt von nachher.php.
 */
final class Warenkorb
{
    private $waehrung;

    private $positionen;

    public function __construct(string $waehrung)
    {
        $this->waehrung = $waehrung;
        $this->positionen = array();
    }

    public function hinzufuegen(string $name, int $centBetrag)
    {
        $this->positionen[] = array('name' => $name, 'cent' => $centBetrag);
    }

    public function summeInCent()
    {
        $summe = 0;
        foreach ($this->positionen as $position) {
            $summe = $summe + $position['cent'];
        }

        return $summe;
    }

    public function enthält($suchbegriff)
    {
        foreach ($this->positionen as $position) {
            if (strpos($position['name'], $suchbegriff) !== false) {
                return true;
            }
        }

        return false;
    }

    public function zusammenfassung()
    {
        return count($this->positionen) . ' Position(en), '
            . number_format($this->summeInCent() / 100, 2, ',', '.')
            . ' ' . $this->waehrung;
    }
}
