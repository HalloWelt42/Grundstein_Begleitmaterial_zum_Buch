<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 67: Internationalisierung
 *
 * NumberFormatter zeigt dieselbe neutral gespeicherte Zahl in mehreren
 * Sprachräumen: als Dezimalzahl, als Prozentwert und als Geldbetrag. Der
 * gespeicherte Wert (ein float, ein int an Cent) bleibt sprachneutral;
 * lokalisiert wird ausschließlich zur Anzeige.
 */

// Eine einzige, neutral gespeicherte Zahl.
$zahl = 1234567.891;

echo '--- Dezimalzahl (immer 1234567.891) ---' . PHP_EOL;
foreach (['de-DE', 'en-US', 'en-IN'] as $locale) {
    $formatierer = new NumberFormatter($locale, NumberFormatter::DECIMAL);
    printf('%-6s %s%s', $locale, $formatierer->format($zahl), PHP_EOL);
}

echo PHP_EOL . '--- Prozent (immer der Anteil 0.0785) ---' . PHP_EOL;
$anteil = 0.0785; // 7,85 Prozent
foreach (['de-DE', 'en-US', 'tr-TR'] as $locale) {
    $formatierer = new NumberFormatter($locale, NumberFormatter::PERCENT);
    $formatierer->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
    printf('%-6s %s%s', $locale, $formatierer->format($anteil), PHP_EOL);
}

echo PHP_EOL . '--- Währung (immer 49,90 EUR) ---' . PHP_EOL;
$cent = 4990; // neutral gespeichert: 4990 Cent
foreach (['de-DE', 'en-US', 'fr-FR'] as $locale) {
    $formatierer = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    // formatCurrency erwartet den Wert in ganzen Einheiten, nicht in Cent.
    printf('%-6s %s%s', $locale, $formatierer->formatCurrency($cent / 100, 'EUR'), PHP_EOL);
}

echo PHP_EOL . '--- Gleicher Sprachraum, andere Währung (de-DE) ---' . PHP_EOL;
foreach (['EUR', 'USD', 'JPY'] as $waehrung) {
    $formatierer = new NumberFormatter('de-DE', NumberFormatter::CURRENCY);
    printf('%-4s %s%s', $waehrung, $formatierer->formatCurrency(4990 / 100, $waehrung), PHP_EOL);
}
