-- Schema der Auftragswarteschlange (Kapitel 61).
-- Jede Zeile ist ein Auftrag. Der Status hält den Lebensweg fest
-- (offen -> in_arbeit -> erledigt oder fehlgeschlagen); die Nutzlast
-- liegt als JSON in der Textspalte "daten".
CREATE TABLE IF NOT EXISTS auftrag (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    typ             TEXT    NOT NULL,
    daten           TEXT    NOT NULL,                 -- Nutzlast als JSON
    status          TEXT    NOT NULL DEFAULT 'offen',
    versuche        INTEGER NOT NULL DEFAULT 0,
    max_versuche    INTEGER NOT NULL DEFAULT 3,
    fehler          TEXT,
    erstellt_am     TEXT    NOT NULL,
    aktualisiert_am TEXT    NOT NULL
);
