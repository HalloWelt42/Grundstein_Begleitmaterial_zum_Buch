<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 18: Namespaces und Autoloading
 *
 * Ein winziger PSR-4-Autoloader von Hand. Er zeigt das Prinzip, das
 * Composer später für dich erzeugt: Der Namespace-Präfix "App\" wird
 * auf das Verzeichnis src/ abgebildet. Aus dem Klassennamen wird ein
 * Dateipfad, der genau dann geladen wird, wenn die Klasse zum ersten Mal
 * gebraucht wird.
 *
 * Im echten Projekt schreibst du diesen Loader NICHT selbst - eine Zeile
 * require 'vendor/autoload.php' genügt, sobald composer.json den
 * psr-4-Block enthält.
 */

spl_autoload_register(function (string $class): void {
    // Der Präfix, den dieser Loader bedient, und der zugehörige Ordner.
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    // Klassen ohne diesen Präfix überlassen wir anderen Loadern.
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    // Rest hinter dem Präfix, z. B. "Catalog\Product".
    $relative = substr($class, strlen($prefix));

    // Backslash wird zum Verzeichnistrenner, ".php" kommt ans Ende.
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
