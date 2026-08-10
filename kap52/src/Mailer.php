<?php

declare(strict_types=1);

namespace App;

/**
 * Vertrag für den Mailversand. Der aufrufende Code weiß nur, dass er
 * etwas verschicken kann - nicht, ob dahinter ein echter Mailserver, ein
 * Protokoll oder im Test eine Attrappe steckt.
 */
interface Mailer
{
    public function sende(string $an, string $betreff): void;
}
