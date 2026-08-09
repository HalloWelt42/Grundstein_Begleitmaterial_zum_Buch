<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 32: Datenzugriff kapseln
 *
 * Ein winziger PSR-4-Autoloader wie in Kapitel 18, damit das Beispiel
 * ohne installiertes Composer läuft. Der Präfix "App\\" zeigt auf den
 * Ordner src/. Im echten Projekt stünde hier stattdessen die eine Zeile
 * require __DIR__ . '/vendor/autoload.php'.
 */
spl_autoload_register(function (string $class): void {
    $prefix  = 'App\\';
    $baseDir = __DIR__ . '/src/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
