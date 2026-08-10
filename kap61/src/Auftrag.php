<?php

declare(strict_types=1);

namespace App;

/**
 * Ein einzelner Auftrag, so wie ihn der Worker aus der Queue erhält.
 *
 * Das Objekt ist unveränderlich (readonly): Es trägt nur die Angaben,
 * die der Worker zum Arbeiten braucht, und hält keinen Zustand über die
 * Bearbeitung hinaus. Genau das schützt einen lange laufenden Worker
 * davor, versehentlich Zustand von einem Auftrag zum nächsten zu
 * schleppen.
 */
final readonly class Auftrag
{
    /**
     * @param int                  $id      Kennung der Zeile in der Tabelle.
     * @param string               $typ     Auftragstyp, etwa "email" oder "bild".
     * @param array<string, mixed> $daten   Entpackte Nutzlast (aus JSON).
     * @param int                  $versuch Laufende Nummer dieses Anlaufs (ab 1).
     */
    public function __construct(
        public int $id,
        public string $typ,
        public array $daten,
        public int $versuch,
    ) {}
}
