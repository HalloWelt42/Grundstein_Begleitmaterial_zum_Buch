-- Grundstein - Kapitel 58: durchgängiges Beispielprojekt
-- Das Schema, gegen das der PDO-Adapter arbeitet. Es lebt bei der
-- Infrastruktur, nicht in der Domäne: Tabellen und Spalten sind ein
-- technisches Detail, von dem der Kern nichts wissen darf.
CREATE TABLE IF NOT EXISTS bestellung (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    kunde       TEXT    NOT NULL,
    betrag_cent INTEGER NOT NULL,
    waehrung    TEXT    NOT NULL DEFAULT 'EUR',
    status      TEXT    NOT NULL DEFAULT 'neu'
);
