-- Migration 002: Der Tabelle kunde eine Spalte email hinzufügen.
-- Ein späterer Wunsch aus der Fachabteilung - als eigener,
-- nachvollziehbarer Schritt, nicht von Hand in der Datenbank.
ALTER TABLE kunde ADD COLUMN email TEXT;
