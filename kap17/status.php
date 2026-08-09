<?php

declare(strict_types=1);

/**
 * Reine Enum ohne hinterlegte Werte. Jeder Fall ist eine feste Konstante
 * vom Typ Himmelsrichtung - mehr Zustände kann es nicht geben.
 */
enum Himmelsrichtung
{
    case Nord;
    case Ost;
    case Sued;
    case West;
}

/**
 * Wertbehaftete (backed) Enum. Jeder Fall trägt zusätzlich einen festen
 * String, etwa für die Ablage in einer Datenbank oder in JSON.
 */
enum Bestellstatus: string
{
    case Offen     = 'offen';
    case Bezahlt   = 'bezahlt';
    case Versandt  = 'versandt';
    case Storniert = 'storniert';
}

// Ein Enum-Fall ist ein vollwertiges Objekt mit festem Typ.
$richtung = Himmelsrichtung::Nord;
var_dump($richtung === Himmelsrichtung::Nord);
var_dump($richtung instanceof Himmelsrichtung);

// Bei einer backed Enum liegt der hinterlegte Wert unter ->value,
// der Name des Falls unter ->name.
$status = Bestellstatus::Bezahlt;
echo $status->name . ' => ' . $status->value . "\n";

// Eine backed Enum serialisiert json_encode() direkt zu ihrem ->value.
echo json_encode($status) . "\n";
echo json_encode(['status' => Bestellstatus::Versandt]) . "\n";

// cases() liefert alle Fälle in Deklarationsreihenfolge als Array.
echo "Alle Status:\n";
foreach (Bestellstatus::cases() as $fall) {
    echo '- ' . $fall->name . ' (' . $fall->value . ")\n";
}

// from() wandelt einen hinterlegten Wert in den passenden Fall um.
$ausDatenbank = 'versandt';
$wieder = Bestellstatus::from($ausDatenbank);
echo 'from(): ' . $wieder->name . "\n";

// tryFrom() liefert bei unbekanntem Wert null statt einer Ausnahme.
$unbekannt = Bestellstatus::tryFrom('gelöscht');
var_dump($unbekannt);

// from() dagegen wirft bei unbekanntem Wert eine ValueError-Ausnahme.
try {
    Bestellstatus::from('gelöscht');
} catch (\ValueError $e) {
    echo 'from() warf: ' . $e->getMessage() . "\n";
}
