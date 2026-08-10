<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use Closure;
use Psr\Container\ContainerInterface;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein kleiner Dependency-Injection-Container (Kapitel 52), der den Standard
 * PSR-11 erfüllt. Sein Kern ist eine Abbildung von Bezeichnern auf
 * Fabrik-Closures. Dienste werden verzögert gebaut (erst beim ersten get) und
 * danach geteilt (jeder weitere get liefert dieselbe Instanz). Auf das
 * Autowiring aus Kapitel 52 verzichten wir hier bewusst: Die Verdrahtung soll
 * an der Kompositionswurzel Zeile für Zeile sichtbar sein.
 */
final class Container implements ContainerInterface
{
    /** @var array<string, Closure(Container): mixed> Registrierte Fabriken. */
    private array $fabriken = [];

    /** @var array<string, mixed> Bereits gebaute, geteilte Instanzen. */
    private array $gebaut = [];

    /**
     * Registriert unter einem Bezeichner eine Fabrik, die den Dienst baut.
     * Der Bezeichner ist meist ein Klassen- oder Interface-Name.
     *
     * @param Closure(Container): mixed $fabrik
     */
    public function set(string $id, Closure $fabrik): void
    {
        $this->fabriken[$id] = $fabrik;
        unset($this->gebaut[$id]); // eine neue Fabrik ersetzt die alte Instanz
    }

    /**
     * PSR-11: Liefert den Dienst zum Bezeichner. Beim ersten Aufruf baut die
     * Fabrik ihn, danach kommt die geteilte Instanz aus dem Zwischenspeicher.
     *
     * @throws NotFoundException wenn der Bezeichner unbekannt ist
     */
    public function get(string $id): mixed
    {
        // Schon gebaut? Dieselbe Instanz zurückgeben (geteilt, Singleton).
        if (array_key_exists($id, $this->gebaut)) {
            return $this->gebaut[$id];
        }

        if (!isset($this->fabriken[$id])) {
            throw new NotFoundException("Kein Eintrag für '{$id}' im Container.");
        }

        // Verzögert bauen und ab jetzt teilen.
        return $this->gebaut[$id] = ($this->fabriken[$id])($this);
    }

    // PSR-11: Kennt der Container einen Eintrag zu diesem Bezeichner?
    public function has(string $id): bool
    {
        return isset($this->fabriken[$id])
            || array_key_exists($id, $this->gebaut);
    }
}
