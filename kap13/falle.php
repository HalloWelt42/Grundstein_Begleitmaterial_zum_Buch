<?php

declare(strict_types=1);

// So NICHT: ein öffentliches, frei veränderbares Feld ohne jede Prüfung.
class KontoOffen
{
    public float $saldo = 0.0;
}

$konto = new KontoOffen();
$konto->saldo = 100.0;
$konto->saldo = -999999.0; // niemand hält das auf
echo 'Saldo außer Kontrolle: ' . $konto->saldo . "\n";
