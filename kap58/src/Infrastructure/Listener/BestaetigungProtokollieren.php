<?php

declare(strict_types=1);

namespace App\Infrastructure\Listener;

use App\Application\BestellungAufgegeben;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein Zuhörer (Kapitel 56), der auf das Ereignis BestellungAufgegeben reagiert.
 * Hier hält er die verschickten Bestätigungen nur in einem Array fest, damit
 * die Demo sie zeigen kann; in einem echten System stieße er den Mailversand
 * an. Der Anwendungsdienst weiß von diesem Zuhörer nichts - hinzu- oder
 * abschalten lässt er sich allein an der Kompositionswurzel.
 */
final class BestaetigungProtokollieren
{
    /** @var list<string> Die verschickten Bestätigungen, für Demo und Test. */
    public array $protokoll = [];

    public function __invoke(BestellungAufgegeben $ereignis): void
    {
        $bestellung = $ereignis->bestellung;

        $this->protokoll[] = sprintf(
            'Bestätigung an %s: Bestellung #%d über %s.',
            $bestellung->kunde->wert,
            $bestellung->id ?? 0,
            $bestellung->betrag->alsText(),
        );
    }
}
