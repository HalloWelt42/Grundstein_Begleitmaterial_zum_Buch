-- Migration 001: Die Tabelle kunde anlegen.
-- Eine reine Vorwärts-Migration im SQL-Format - der erste Schritt,
-- auf dem alle späteren aufbauen.
CREATE TABLE kunde (
    id      INTEGER PRIMARY KEY,   -- Primärschlüssel, eindeutig
    name    TEXT    NOT NULL,      -- Pflichtfeld: darf nicht leer sein
    ort     TEXT    NOT NULL
);
