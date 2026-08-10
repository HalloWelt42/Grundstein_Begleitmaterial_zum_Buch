<?php

declare(strict_types=1);

/**
 * Kurzform für die sichere HTML-Ausgabe. Jeder dynamische Wert läuft
 * durch diese Funktion, bevor er ins HTML kommt - sonst wäre die Seite
 * für Cross-Site-Scripting (XSS) offen. ENT_QUOTES entschärft auch
 * einfache und doppelte Anführungszeichen.
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Rendert ein Template und gibt das erzeugte HTML als Zeichenkette
 * zurück. Die übergebenen Daten werden im Template als lokale
 * Variablen sichtbar; die Ausgabe des Templates fängt ein
 * Ausgabepuffer ein, statt sie sofort zu senden.
 *
 * @param string               $template Pfad zur Template-Datei
 * @param array<string, mixed> $daten    Variablen für das Template
 */
function render(string $template, array $daten = []): string
{
    // Die Schlüssel des Arrays werden zu lokalen Variablen. EXTR_SKIP
    // überschreibt keine schon vorhandene Variable - so kann ein
    // Datenfeld niemals versehentlich $template selbst überschreiben.
    extract($daten, EXTR_SKIP);

    // Ab hier landet jede Ausgabe im Puffer statt auf dem Bildschirm.
    ob_start();
    require $template;

    // Den gepufferten Inhalt holen, den Puffer schließen, zurückgeben.
    return (string) ob_get_clean();
}

// --- Logik: die Daten aufbereiten ------------------------------------
// In einer echten Anwendung kämen diese Daten aus einer Datenbank oder
// aus der Anfrage. Hier stehen sie fest - entscheidend ist, dass die
// Logik sie vollständig vorbereitet, bevor das Template ins Spiel kommt.
$aufgaben = [
    ['text' => 'Einkaufen gehen',            'erledigt' => true],
    ['text' => 'Kapitel <Templating> lesen', 'erledigt' => false],
    ['text' => 'Test "grün" schreiben',      'erledigt' => false],
];

// --- Darstellung: das Template mit den Daten füllen ------------------
echo render(__DIR__ . '/templates/liste.php', [
    'titel'    => 'Meine Aufgaben',
    'aufgaben' => $aufgaben,
]);
