<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 49: Testbaren Code schreiben (Nachher)
 *
 * Die Produktions-Verdrahtung: Hier - und nur hier, am Rand der
 * Anwendung - werden die echten Umsetzungen zusammengesteckt. Der Dienst
 * bekommt die Systemuhr und die Zufallsquelle hereingereicht. Im Test
 * treten an dieselben Nähte feste Doubles.
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Gutscheindienst;
use App\SystemClock;
use App\ZufallsCodeQuelle;

$dienst = new Gutscheindienst(new SystemClock(), new ZufallsCodeQuelle());

$gutschein = $dienst->stelleAus(2500);

echo 'Code:       ' . $gutschein->code . PHP_EOL;
echo 'Wert:       ' . number_format($gutschein->wertCent / 100, 2, ',', '.') . ' EUR' . PHP_EOL;
echo 'Gültig bis: ' . $gutschein->gueltigBis->format('d.m.Y') . PHP_EOL;
