<?php

declare(strict_types=1);

namespace App\Adapter\Payment;

use App\Application\ZahlungAbgelehnt;
use App\Application\ZahlungsPort;
use App\Domain\Geld;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Ein getriebener Adapter für den Port ZahlungsPort. Er steht stellvertretend
 * für einen echten Zahlungsanbieter: Statt tatsächlich Geld zu bewegen, prüft
 * er nur gegen ein festes Limit und lehnt zu große Beträge mit einer
 * ZahlungAbgelehnt ab. In einer echten Anwendung spräche an dieser Stelle der
 * Aufruf zum Anbieter - der Kern merkte davon nichts.
 */
final class BegrenzteZahlung implements ZahlungsPort
{
    public function __construct(
        private readonly Geld $limit,
    ) {}

    public function belaste(Geld $betrag, string $referenz): void
    {
        // Über dem Limit? Dann lehnt der Anbieter ab - als fachliche Ausnahme.
        if ($betrag->cent > $this->limit->cent) {
            throw new ZahlungAbgelehnt(sprintf(
                'Betrag %s über dem Limit %s (%s).',
                $betrag->alsText(),
                $this->limit->alsText(),
                $referenz,
            ));
        }

        // Sonst gilt die Belastung als erfolgreich. Hier spräche der echte
        // Adapter mit dem Zahlungsanbieter.
    }
}
