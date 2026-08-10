-- Grundstein - Kapitel 55: Ports und Adapter
-- Das Schema, gegen das der PDO-Adapter arbeitet. Es lebt beim Adapter,
-- nicht in der Domäne: Tabellen und Spalten sind ein technisches Detail,
-- von dem der Kern nichts wissen darf.
CREATE TABLE bestellung (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    kunde       TEXT    NOT NULL,
    betrag_cent INTEGER NOT NULL,
    waehrung    TEXT    NOT NULL DEFAULT 'EUR',
    bezahlt     INTEGER NOT NULL DEFAULT 0
);
