<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 10: Funktionen
 *
 * Zeigt, wie du Funktionen deklarierst und aufrufst, Parameter typisierst
 * und mit Defaultwerten versiehst, benannte Argumente nutzt, variadische
 * Parameter sammelst, Rückgabetypen (inklusive void und never) angibst,
 * Union-Types einsetzt und den Unterschied zwischen Wert- und
 * Referenzübergabe verstehst. Alle Ausgaben stammen aus einem echten
 * Lauf mit PHP 8.4.
 */

// --- Deklarieren und aufrufen ------------------------------------------

/**
 * Begrüßt eine Person mit ihrem Namen.
 */
function greet(string $name): string
{
    return "Hallo, {$name}!";
}

echo greet('Ada') . "\n";
echo greet('Grace') . "\n";

// --- Parameter mit Typen und Defaultwerten -----------------------------

/**
 * Berechnet den Bruttopreis. Der Steuersatz hat einen Defaultwert und
 * darf beim Aufruf weggelassen werden.
 */
function brutto(float $netto, float $satz = 0.19): float
{
    return $netto * (1 + $satz);
}

echo "\n";
printf("Standard 19%%: %.2f\n", brutto(100.0));
printf("Ermäßigt  7%%: %.2f\n", brutto(100.0, 0.07));

// --- Benannte Argumente ------------------------------------------------

/**
 * Formatiert einen Betrag mit wählbaren Trennzeichen.
 */
function formatiereGeld(
    float $betrag,
    int $nachkomma = 2,
    string $dezimal = ',',
    string $tausender = '.',
): string {
    return number_format($betrag, $nachkomma, $dezimal, $tausender);
}

echo "\n";
// Ohne benannte Argumente: was das dritte und vierte Argument bedeutet,
// muss man erraten oder nachschlagen.
echo formatiereGeld(1234.5, 2, ',', '.') . "\n";
// Mit benannten Argumenten steht die Absicht direkt im Aufruf. Die
// Defaultwerte der übersprungenen Parameter greifen automatisch.
echo formatiereGeld(1234.5, tausender: ' ') . "\n";

// --- Variadische Parameter ---------------------------------------------

/**
 * Summiert beliebig viele Zahlen. Die einzelnen Werte sammelt PHP im
 * Array $werte.
 */
function summe(int|float ...$werte): int|float
{
    $ergebnis = 0;
    foreach ($werte as $wert) {
        $ergebnis += $wert;
    }
    return $ergebnis;
}

echo "\n";
echo 'Summe (3 Zahlen): ' . summe(1, 2, 3) . "\n";
echo 'Summe (5 Zahlen): ' . summe(10, 20, 30, 40, 50) . "\n";

// Ein fertiges Array entpackt der Spread-Operator ... beim Aufruf.
$zahlen = [2, 4, 6, 8];
echo 'Summe (Array):    ' . summe(...$zahlen) . "\n";

// --- Rückgabetyp void ---------------------------------------------------

/**
 * Gibt eine Protokollzeile aus und liefert bewusst nichts zurück (void).
 */
function protokolliere(string $text): void
{
    echo "[LOG] {$text}\n";
}

echo "\n";
protokolliere('Programm gestartet');

// --- Rückgabetyp never --------------------------------------------------

/**
 * Bricht mit einer Ausnahme ab. Der Typ never sagt: diese Funktion kehrt
 * nie normal zurück - sie wirft immer oder beendet das Programm.
 */
function abbruch(string $grund): never
{
    throw new RuntimeException($grund);
}

echo "\n";
try {
    abbruch('Ungültige Eingabe');
} catch (RuntimeException $e) {
    echo 'Abgefangen: ' . $e->getMessage() . "\n";
}

// --- Union-Types --------------------------------------------------------

/**
 * Liefert die Länge - bei einem Text die Zahl der Zeichen, bei einem
 * Array die Zahl der Elemente. Der Parameter akzeptiert beide Typen.
 */
function groesseVon(string|array $wert): int
{
    return is_string($wert) ? mb_strlen($wert) : count($wert);
}

echo "\n";
echo 'Länge Text:  ' . groesseVon('Grüße') . "\n";
echo 'Länge Array: ' . groesseVon([1, 2, 3, 4]) . "\n";

// --- Wert- vs. Referenzübergabe ----------------------------------------

/**
 * Verdoppelt nur die lokale Kopie - die Variable des Aufrufers bleibt
 * unberührt (Wertübergabe, der Normalfall).
 */
function verdoppleWert(int $zahl): void
{
    $zahl *= 2;
}

/**
 * Verdoppelt die Variable des Aufrufers direkt. Das & macht aus dem
 * Parameter eine Referenz auf dieselbe Variable.
 */
function verdoppleReferenz(int &$zahl): void
{
    $zahl *= 2;
}

echo "\n";
$a = 5;
verdoppleWert($a);
echo "Nach Wertübergabe:     {$a}\n";

$b = 5;
verdoppleReferenz($b);
echo "Nach Referenzübergabe: {$b}\n";

// --- Gültigkeitsbereich -------------------------------------------------

$outer = 'draußen';

/**
 * Eine Funktion sieht die Variablen ihres Umfelds nicht. Sie arbeitet in
 * einem eigenen, lokalen Gültigkeitsbereich.
 */
function eigenerBereich(): string
{
    $innen = 'drinnen';
    return $innen;
}

echo "\n";
echo eigenerBereich() . "\n";
echo "Außen unverändert: {$outer}\n";
