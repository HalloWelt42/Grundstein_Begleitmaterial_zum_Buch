-- Schema für das Kunden- und Bestellbeispiel aus Kapitel 30.
-- Zwei Tabellen, verbunden über einen Fremdschlüssel: Ein Kunde hat
-- viele Bestellungen (1:n). Läuft unverändert in SQLite; für
-- PostgreSQL und MariaDB nur die Typnamen anpassen (siehe Kapitel-Text).

-- Fremdschlüssel in SQLite ausdrücklich einschalten.
PRAGMA foreign_keys = ON;

-- Die "eine" Seite der Beziehung: jeder Kunde genau einmal.
CREATE TABLE kunde (
    id      INTEGER PRIMARY KEY,   -- Primärschlüssel, eindeutig
    name    TEXT    NOT NULL,      -- Pflichtfeld: darf nicht leer sein
    stadt   TEXT    NOT NULL
);

-- Die "viele" Seite: beliebig viele Bestellungen je Kunde.
CREATE TABLE bestellung (
    id          INTEGER PRIMARY KEY,
    kunde_id    INTEGER NOT NULL,      -- zeigt auf kunde.id
    artikel     TEXT    NOT NULL,
    betrag_cent INTEGER NOT NULL,      -- Geld immer als ganze Cent
    bestellt_am TEXT    NOT NULL,      -- ISO-Datum als Text: YYYY-MM-DD
    -- Der Fremdschlüssel garantiert: kunde_id muss es wirklich geben.
    FOREIGN KEY (kunde_id) REFERENCES kunde (id)
);
