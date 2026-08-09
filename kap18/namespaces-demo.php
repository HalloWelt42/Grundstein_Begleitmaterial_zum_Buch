<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 18: Namespaces und Autoloading
 *
 * Teil 1: Namespaces gegen Namenskollisionen. Voll qualifizierte Namen
 * (FQN) mit führendem Backslash, der use-Import, Aliasse mit "as" sowie
 * das Importieren von Funktionen und Konstanten.
 *
 * Ausnahmsweise stehen hier mehrere Namespaces in EINER Datei
 * (Klammer-Syntax), nur um die Kollision an einem Ort zu zeigen. Im
 * echten Projekt gehört jeder Namespace in seine eigene Datei.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

// --- Erstes Modul: ein PDF-Schreiber -----------------------------------

namespace Shop\Pdf {
    /**
     * Schreibt Text als PDF-Zeile. Der Klassenname "Writer" ist bewusst
     * so gewählt, dass er gleich noch einmal auftaucht.
     */
    final class Writer
    {
        public function write(string $text): string
        {
            return "[PDF] {$text}";
        }
    }

    // Eine Konstante und eine Funktion in diesem Namespace.
    const VERSION = '1.0-pdf';

    function marke(): string
    {
        return 'PDF-Modul';
    }
}

// --- Zweites Modul: ein CSV-Schreiber ----------------------------------

namespace Shop\Csv {
    /**
     * Ebenfalls ein "Writer" - gleicher kurzer Name, aber kein Konflikt,
     * weil er in einem anderen Namespace lebt.
     */
    final class Writer
    {
        public function write(string $text): string
        {
            return "[CSV] {$text}";
        }
    }
}

// --- Anwendungscode ----------------------------------------------------

namespace App {
    // use-Anweisungen stehen ganz oben im Namespace, vor allem anderen.
    use Shop\Pdf\Writer;              // kurzer Name: Writer
    use Shop\Csv\Writer as CsvWriter; // Alias, weil Writer schon belegt ist
    use const Shop\Pdf\VERSION;       // Konstante importieren
    use function Shop\Pdf\marke;      // Funktion importieren

    // Dank Import genügt der kurze Name.
    $pdf = new Writer();
    echo $pdf->write('Rechnung') . "\n";

    // Der Alias löst die Kollision auf.
    $csv = new CsvWriter();
    echo $csv->write('Rechnung') . "\n";

    // Ganz ohne Import geht es mit dem voll qualifizierten Namen (FQN).
    // Der führende Backslash bedeutet: beginne an der Wurzel.
    $auch = new \Shop\Csv\Writer();
    echo $auch->write('direkt') . "\n";

    // Importierte Konstante und Funktion, ebenfalls mit kurzem Namen.
    echo 'Version: ' . VERSION . ', Herkunft: ' . marke() . "\n";
}
