<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\Mailer;

/*
 * Grundstein - Kapitel 48: Test-Doubles
 *
 * Ein handgeschriebener Spy: Er verschickt nichts, sondern schreibt jeden
 * Versand in ein öffentliches Array. Nach dem Aufruf des SUT liest der
 * Test daraus ab, ob und womit versendet wurde - ein Spy prüft also erst
 * im Nachhinein.
 */
final class SpyMailer implements Mailer
{
    /** @var list<array{an: string, betreff: string, text: string}> */
    public array $versendet = [];

    public function versende(string $an, string $betreff, string $text): void
    {
        $this->versendet[] = ['an' => $an, 'betreff' => $betreff, 'text' => $text];
    }
}
