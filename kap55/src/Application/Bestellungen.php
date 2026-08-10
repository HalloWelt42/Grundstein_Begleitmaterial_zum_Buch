<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Bestellung;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Ein GETRIEBENER Port (secondary port) für die Persistenz. Die Anwendung
 * BESITZT dieses Interface und formuliert es in ihrer eigenen Sprache -
 * kein Wort von SQL, PDO oder Tabellen. Wer die Bestellungen speichert,
 * bleibt offen: Ein Adapter setzt den Vertrag mit einer echten Datenbank um,
 * ein anderer nur mit einem Array im Speicher. Genau das ist die umgekehrte
 * Abhängigkeit - die Technik richtet sich nach der Anwendung, nicht andersherum.
 */
interface Bestellungen
{
    // Findet genau eine Bestellung oder null, wenn es sie nicht gibt.
    public function find(int $id): ?Bestellung;

    // Speichert eine Bestellung und gibt sie mit gültiger id zurück.
    public function save(Bestellung $bestellung): Bestellung;

    /** @return list<Bestellung> Alle noch nicht bezahlten Bestellungen. */
    public function alleOffenen(): array;
}
