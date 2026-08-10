<?php

declare(strict_types=1);

/**
 * Kleiner, wiederverwendbarer Prüfer für Formulareingaben.
 *
 * Er sammelt Fehlermeldungen, statt beim ersten Problem abzubrechen -
 * so bekommt der Nutzer alle Fehler eines Formulars auf einmal genannt
 * und muss nicht nach jeder Korrektur erneut ins Messer laufen.
 *
 * Pro Feld wird nur die erste Meldung behalten: Ist ein Pflichtfeld
 * leer, ist eine zusätzliche Formatmeldung für dasselbe Feld nur
 * verwirrend.
 */
final class Validator
{
    /** @var array<string, string> Feldname => erste Fehlermeldung */
    private array $fehler = [];

    /**
     * Das Feld muss vorhanden und nach dem Entfernen von Leerraum
     * nicht leer sein.
     */
    public function pflicht(string $feld, string $wert, string $meldung): self
    {
        if (trim($wert) === '') {
            $this->merke($feld, $meldung);
        }

        return $this;
    }

    /**
     * Das Feld muss eine syntaktisch gültige E-Mail-Adresse sein.
     * Ein leeres Feld wird übergangen - dafür ist pflicht() zuständig.
     */
    public function email(string $feld, string $wert, string $meldung): self
    {
        if ($wert !== '' && filter_var($wert, FILTER_VALIDATE_EMAIL) === false) {
            $this->merke($feld, $meldung);
        }

        return $this;
    }

    /**
     * Das Feld muss mindestens $min Zeichen lang sein. Gezählt wird
     * mit mb_strlen, damit Umlaute als ein Zeichen zählen. Ein leeres
     * Feld wird übergangen - dafür ist pflicht() zuständig.
     */
    public function minLaenge(string $feld, string $wert, int $min, string $meldung): self
    {
        if ($wert !== '' && mb_strlen($wert) < $min) {
            $this->merke($feld, $meldung);
        }

        return $this;
    }

    /**
     * Das Feld muss eine ganze Zahl im geschlossenen Bereich
     * [$min, $max] sein. Ein leeres Feld wird übergangen.
     */
    public function ganzzahlBereich(string $feld, string $wert, int $min, int $max, string $meldung): self
    {
        if ($wert === '') {
            return $this;
        }

        $zahl = filter_var($wert, FILTER_VALIDATE_INT);
        if ($zahl === false || $zahl < $min || $zahl > $max) {
            $this->merke($feld, $meldung);
        }

        return $this;
    }

    /**
     * Sind bisher gar keine Fehler aufgetreten?
     */
    public function istGueltig(): bool
    {
        return $this->fehler === [];
    }

    /**
     * Die Fehlermeldung für ein Feld - oder ein leerer String, wenn
     * das Feld fehlerfrei ist.
     */
    public function fehlerFuer(string $feld): string
    {
        return $this->fehler[$feld] ?? '';
    }

    /**
     * Alle gesammelten Meldungen als Liste, etwa für eine
     * Zusammenfassung oben im Formular.
     *
     * @return list<string>
     */
    public function alleMeldungen(): array
    {
        return array_values($this->fehler);
    }

    /**
     * Merkt sich die Meldung, aber nur die erste je Feld.
     */
    private function merke(string $feld, string $meldung): void
    {
        if (!isset($this->fehler[$feld])) {
            $this->fehler[$feld] = $meldung;
        }
    }
}
