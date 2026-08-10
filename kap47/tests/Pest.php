<?php

declare(strict_types=1);

/*
 * Zentrale Pest-Konfiguration. Hier werden gemeinsame Datensätze
 * definiert und - bei Bedarf - eine Basis-Testklasse an Verzeichnisse
 * gebunden. Für diese schlanke Suite genügt ein wiederverwendbarer
 * Datensatz.
 */

// Ein benannter Datensatz: Prozentsatz und erwarteter Abzug bei 10000 Cent.
dataset('rabattsaetze', [
    'zehn Prozent'      => [10, 1000],
    'zwanzig Prozent'   => [20, 2000],
    'voller Nachlass'   => [100, 10000],
    'gar kein Nachlass' => [0, 0],
]);
