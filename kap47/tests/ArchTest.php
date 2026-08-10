<?php

declare(strict_types=1);

// Architektur-Tests prüfen nicht das Verhalten, sondern die Struktur
// des Codes - hier: dass die Domänenschicht sauber bleibt.

arch('die Domäne gibt nichts aus')
    ->expect('App')
    ->not->toUse(['var_dump', 'dd', 'echo', 'print_r']);

arch('die Domäne kennt keine Datenbank')
    ->expect('App')
    ->not->toUse('PDO');

arch('der ganze Code nutzt den strikten Modus')
    ->expect('App')
    ->toUseStrictTypes();
