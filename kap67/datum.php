<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 67: Internationalisierung
 *
 * IntlDateFormatter macht aus einem DateTimeImmutable (Kapitel 66) eine
 * sprachlich korrekte Datums- und Zeitausgabe. Der Zeitpunkt kommt neutral
 * in UTC herein; Sprache und Anzeige-Zone bestimmt der Formatierer. Genau
 * das schließt die offene Flanke aus Kapitel 66, wo format() die Namen nur
 * auf Englisch lieferte.
 */

// Ein Augenblick, neutral in UTC gehalten (die goldene Regel aus Kapitel 66).
$augenblick = new DateTimeImmutable('2026-03-28 18:05:00', new DateTimeZone('UTC'));

echo '--- Dasselbe Datum in mehreren Sprachräumen (LONG + SHORT) ---' . PHP_EOL;
foreach (['de-DE', 'en-US', 'es-ES'] as $locale) {
    $formatierer = new IntlDateFormatter(
        $locale,
        IntlDateFormatter::LONG,   // Länge des Datumsteils
        IntlDateFormatter::SHORT,  // Länge des Zeitteils
        'Europe/Berlin',           // Zone der Anzeige
    );
    printf('%-6s %s%s', $locale, $formatierer->format($augenblick), PHP_EOL);
}

echo PHP_EOL . '--- Vier Längenstufen (de-DE, nur Datum) ---' . PHP_EOL;
$stufen = [
    'FULL'   => IntlDateFormatter::FULL,
    'LONG'   => IntlDateFormatter::LONG,
    'MEDIUM' => IntlDateFormatter::MEDIUM,
    'SHORT'  => IntlDateFormatter::SHORT,
];
foreach ($stufen as $name => $stufe) {
    $formatierer = new IntlDateFormatter('de-DE', $stufe, IntlDateFormatter::NONE, 'Europe/Berlin');
    printf('%-7s %s%s', $name, $formatierer->format($augenblick), PHP_EOL);
}

echo PHP_EOL . '--- Eigenes Muster statt fester Stufe (de-DE) ---' . PHP_EOL;
// setPattern erlaubt ein maßgeschneidertes Muster in ICU-Schreibweise:
// EEEE = ausgeschriebener Wochentag, d = Tag, MMMM = Monatsname, y = Jahr.
$formatierer = new IntlDateFormatter('de-DE', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Berlin');
$formatierer->setPattern("EEEE, 'den' d. MMMM y");
echo $formatierer->format($augenblick) . PHP_EOL;
