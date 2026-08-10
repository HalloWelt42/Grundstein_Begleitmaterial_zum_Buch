<?php

declare(strict_types=1);

/*
 * Erzeugt eine große Bibliothek aus vielen gleichartigen Klassen, damit es
 * beim Laden wirklich etwas zu parsen und zu kompilieren gibt. So lässt sich
 * der Kompilierschritt messbar machen - ein einzelnes kleines Skript wäre
 * dafür viel zu klein.
 *
 * Aufruf:
 *   php lib-erzeugen.php lib.php 6000
 *
 * Das Ergebnis (lib.php) ist ein generiertes Artefakt und gehört nicht ins
 * Versionsverwaltungssystem - es wird bei Bedarf neu erzeugt.
 */

$ziel   = $argv[1] ?? 'lib.php';
$anzahl = (int) ($argv[2] ?? 6000);

$puffer = "<?php\n\ndeclare(strict_types=1);\n\n";

for ($i = 0; $i < $anzahl; $i++) {
    // Jede Klasse trägt ein paar Methoden mit etwas Rechenarbeit, damit
    // der Quelltext eine realistische Größe bekommt.
    $puffer .= <<<KLASSE
        final class Rechenknoten{$i}
        {
            public function wert(): int
            {
                return {$i} * 2 + 1;
            }

            public function summe(int \$a, int \$b): int
            {
                return \$a + \$b + {$i};
            }

            public function beschreibung(): string
            {
                return 'Knoten Nummer {$i}';
            }
        }


        KLASSE;
}

file_put_contents($ziel, $puffer);

$groesseKiB = (int) round(strlen($puffer) / 1024);
printf('%s geschrieben: %d Klassen, %d KiB%s', $ziel, $anzahl, $groesseKiB, PHP_EOL);
