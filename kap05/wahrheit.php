<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 5: Wahrheitswerte (truthy und falsy).
 *
 * In einer Bedingung wandelt PHP jeden Wert nach bool. Die meisten Werte
 * sind "truthy" (gelten als true); nur eine überschaubare Liste ist
 * "falsy". Dieses Skript prüft eine Reihe von Werten und zeigt, welche
 * als false gelten.
 */

// Eine gemischte Liste von Werten, die wir der Reihe nach prüfen.
$werte = [0, 1, -1, 0.0, '', '0', '0.0', 'false', [], [0], null];

foreach ($werte as $wert) {
    $typ = get_debug_type($wert);

    // Eine kurze, einzeilige Darstellung des Werts fürs Protokoll.
    $zeigt = is_array($wert)
        ? '[' . implode(', ', $wert) . ']'
        : var_export($wert, true);

    // Der Kern: (bool) macht aus dem Wert einen echten Wahrheitswert.
    $urteil = (bool) $wert ? 'truthy' : 'falsy';

    printf('%-8s %-8s -> %s%s', $typ, $zeigt, $urteil, PHP_EOL);
}
