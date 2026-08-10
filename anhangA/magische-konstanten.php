<?php

declare(strict_types=1);

namespace App\Referenz;

echo 'Zeile:     ' . __LINE__ . "\n";
echo 'Namespace: ' . __NAMESPACE__ . "\n";

function wer(): string
{
    return __FUNCTION__;   // im Namespace voll qualifiziert
}
echo 'Funktion:  ' . wer() . "\n";

class Dienst
{
    public function melde(): string
    {
        return __METHOD__;   // Klasse::Methode
    }
}

echo 'class:     ' . Dienst::class . "\n";
echo 'Methode:   ' . (new Dienst())->melde() . "\n";
