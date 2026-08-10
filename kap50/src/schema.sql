-- Grundstein - Kapitel 50: Integrationstests
--
-- Das Schema für die Tabelle kunde, wie es der Integrationstest in einer
-- frischen Datenbank anlegt. In einem echten Projekt entstünde dieses
-- Schema aus den Migrationen (Kapitel 33) - der Integrationstest lässt
-- die gleiche Struktur entstehen, gegen die auch die Anwendung läuft.
--
-- Diese Fassung ist für SQLite geschrieben (INTEGER PRIMARY KEY als
-- id-Vergabe). Für PostgreSQL oder MariaDB sähe die id-Spalte leicht
-- anders aus (SERIAL bzw. AUTO_INCREMENT); der übrige PDO-Code bleibt
-- gleich.

CREATE TABLE kunde (
    id          INTEGER PRIMARY KEY,
    name        TEXT    NOT NULL,
    email       TEXT    NOT NULL UNIQUE,
    umsatz_cent INTEGER NOT NULL DEFAULT 0
);
