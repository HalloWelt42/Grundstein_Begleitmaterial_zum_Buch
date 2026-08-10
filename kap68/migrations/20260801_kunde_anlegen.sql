-- Additive Migration: eine neue Tabelle. Die alte Version kennt sie noch
-- nicht, kommt aber ohne sie aus - das Schema bleibt abwärtsverträglich.
CREATE TABLE IF NOT EXISTS kunde (
    id    INTEGER PRIMARY KEY,
    email TEXT NOT NULL,
    name  TEXT NOT NULL
);
