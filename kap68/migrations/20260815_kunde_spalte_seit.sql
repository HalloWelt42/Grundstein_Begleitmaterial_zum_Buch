-- Additive Migration: eine neue Spalte, anfangs leer (NULL). Bestehende
-- Zeilen und die alte Version bleiben gültig - kein zerstörerischer Schritt.
ALTER TABLE kunde ADD COLUMN seit TEXT;
