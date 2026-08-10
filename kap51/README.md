# Grundstein - Kapitel 51: Continuous Integration

[![CI](https://github.com/DEIN-KONTO/DEIN-REPO/actions/workflows/ci.yml/badge.svg)](https://github.com/DEIN-KONTO/DEIN-REPO/actions/workflows/ci.yml)

Das Beispielprojekt zu Kapitel 51. Es zeigt eine vollständige Prüfkette,
die GitHub Actions bei jedem Push und jedem Pull Request automatisch
ausführt: Tests (PHPUnit), statische Analyse (PHPStan) und Stil-Prüfung
(PHP-CS-Fixer).

## Lokal prüfen

Alle drei Prüfungen zusammen laufen mit einem einzigen Befehl:

    composer install
    composer pruefe

Einzeln gehen sie auch:

    composer test
    composer analyse
    composer stil

## Das Abzeichen

Der Baustein ganz oben ist das Status-Abzeichen. Es zeigt live, ob der
letzte Lauf auf dem Hauptzweig grün oder rot war. Ersetze `DEIN-KONTO`
und `DEIN-REPO` durch deinen eigenen Konto- und Projektnamen.
