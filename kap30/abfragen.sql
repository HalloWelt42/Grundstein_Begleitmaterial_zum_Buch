-- Die wichtigsten SQL-Befehle am Kunden- und Bestellbeispiel.
-- Reihenfolge wie im Kapitel: einfügen, lesen, ändern, löschen, verbinden.

-- 1. Daten einfügen (INSERT).
INSERT INTO kunde (id, name, stadt) VALUES
    (1, 'Anna Krüger',  'Lüneburg'),
    (2, 'Björn Möller', 'Köln'),
    (3, 'Clara Groß',   'München');

INSERT INTO bestellung (id, kunde_id, artikel, betrag_cent, bestellt_am) VALUES
    (1, 1, 'Schraubenschlüssel', 1299, '2026-02-03'),
    (2, 1, 'Zollstock',           599, '2026-02-05'),
    (3, 2, 'Wasserwaage',        2450, '2026-02-04'),
    (4, 3, 'Akkuschrauber',      8990, '2026-02-06'),
    (5, 3, 'Bohrer-Set',         1590, '2026-02-07');

-- 2. Alles lesen (SELECT).
SELECT id, name, stadt FROM kunde;

-- 3. Filtern und sortieren (WHERE, ORDER BY).
SELECT artikel, betrag_cent
FROM bestellung
WHERE betrag_cent >= 1500
ORDER BY betrag_cent DESC;

-- 4. Ändern (UPDATE): einen Preis korrigieren.
UPDATE bestellung
SET betrag_cent = 1990
WHERE id = 2;

-- 5. Löschen (DELETE): eine Bestellung entfernen.
DELETE FROM bestellung
WHERE id = 5;

-- 6. Zwei Tabellen verbinden (JOIN): welcher Kunde bestellte was?
SELECT kunde.name, bestellung.artikel, bestellung.betrag_cent
FROM bestellung
JOIN kunde ON kunde.id = bestellung.kunde_id
ORDER BY kunde.name;
