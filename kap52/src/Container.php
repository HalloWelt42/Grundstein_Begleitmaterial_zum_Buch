<?php

declare(strict_types=1);

namespace App;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Ein kleiner, aber vollständiger Dependency-Injection-Container.
 *
 * Er erfüllt den Standard PSR-11 (ContainerInterface) und lässt sich
 * damit überall dort einsetzen, wo ein PSR-11-Container erwartet wird.
 * Sein Kern ist eine Abbildung von Bezeichnern auf Fabrik-Closures. Die
 * Dienste werden verzögert gebaut (erst beim ersten get) und danach
 * geteilt (jeder weitere get liefert dieselbe Instanz).
 */
final class Container implements ContainerInterface
{
    /**
     * Registrierte Fabriken: Bezeichner -> Closure, die den Dienst baut.
     *
     * @var array<string, Closure(Container): mixed>
     */
    private array $fabriken = [];

    /**
     * Bereits gebaute, geteilte Instanzen (das Singleton-Verhalten).
     *
     * @var array<string, mixed>
     */
    private array $gebaut = [];

    /**
     * Bezeichner, die gerade im Bau sind - die Wache gegen Zyklen.
     *
     * @var array<string, true>
     */
    private array $imBau = [];

    /**
     * Registriert unter einem Bezeichner eine Fabrik, die den Dienst
     * baut. Die Fabrik bekommt den Container hereingereicht und kann
     * darüber ihre eigenen Abhängigkeiten anfordern.
     *
     * @param Closure(Container): mixed $fabrik
     */
    public function set(string $id, Closure $fabrik): void
    {
        $this->fabriken[$id] = $fabrik;

        // Eine neu gesetzte Fabrik macht eine früher gebaute Instanz
        // ungültig - sie wird beim nächsten get() frisch erzeugt.
        unset($this->gebaut[$id]);
    }

    /**
     * PSR-11: Meldet, ob der Container zu diesem Bezeichner einen Eintrag
     * hat. true steht für eine registrierte Fabrik, eine schon gebaute
     * Instanz oder eine existierende Klasse. Die PSR-11-Zusicherung dazu:
     * Bei has() == true wirft get() nie ein "nicht gefunden" - das Bauen
     * darf aber sehr wohl mit einer ContainerException scheitern, etwa bei
     * einer existierenden Klasse mit ungebundener Interface-Abhängigkeit.
     */
    public function has(string $id): bool
    {
        return isset($this->fabriken[$id])
            || array_key_exists($id, $this->gebaut)
            // Existierende Klasse: get() findet sie und wirft kein NotFound.
            // Ob das Bauen gelingt, entscheidet sich erst beim get() selbst.
            || class_exists($id);
    }

    /**
     * PSR-11: Liefert den Dienst zum Bezeichner. Beim ersten Aufruf wird
     * er gebaut, danach aus dem Zwischenspeicher zurückgegeben.
     *
     * @throws NotFoundException  wenn der Bezeichner unbekannt ist
     * @throws ContainerException wenn der Dienst nicht gebaut werden kann
     */
    public function get(string $id): mixed
    {
        // Schon gebaut? Dieselbe Instanz zurückgeben (Singleton).
        if (array_key_exists($id, $this->gebaut)) {
            return $this->gebaut[$id];
        }

        // Bauen wir diesen Bezeichner gerade schon? Dann beißt sich der
        // Objektgraph in den Schwanz - ein Zyklus.
        if (isset($this->imBau[$id])) {
            throw new ContainerException("Zyklische Abhängigkeit bei '{$id}'.");
        }

        // Weder Fabrik noch bekannte Klasse: der Container kennt es nicht.
        if (!isset($this->fabriken[$id]) && !class_exists($id)) {
            throw new NotFoundException("Kein Eintrag für '{$id}' im Container.");
        }

        $this->imBau[$id] = true;
        try {
            // Verzögertes Bauen: entweder über die Fabrik oder, wenn
            // keine registriert ist, über Autowiring der Klasse.
            $dienst = isset($this->fabriken[$id])
                ? ($this->fabriken[$id])($this)
                : $this->autowire($id);
        } finally {
            // Egal ob Erfolg oder Ausnahme: die Bau-Markierung fällt weg.
            unset($this->imBau[$id]);
        }

        // Einmal bauen, ab jetzt teilen.
        return $this->gebaut[$id] = $dienst;
    }

    /**
     * Baut eine Klasse per Reflection: liest ihren Konstruktor aus und
     * löst jeden Parameter selbst wieder über den Container auf. So
     * entsteht der Objektgraph, ohne dass eine Fabrik von Hand jedes
     * "new" hinschreiben muss.
     */
    private function autowire(string $klasse): object
    {
        $spiegel = new ReflectionClass($klasse);

        // Interfaces und abstrakte Klassen lassen sich nicht bauen -
        // hier braucht der Container eine ausdrückliche Fabrik.
        if (!$spiegel->isInstantiable()) {
            throw new ContainerException(
                "'{$klasse}' ist nicht instanziierbar (Interface oder abstrakt); "
                . 'bitte eine Fabrik mit set() registrieren.'
            );
        }

        $konstruktor = $spiegel->getConstructor();

        // Kein Konstruktor: nichts aufzulösen, einfach erzeugen.
        if ($konstruktor === null) {
            return new $klasse();
        }

        // Jeden Konstruktor-Parameter der Reihe nach auflösen.
        $argumente = [];
        foreach ($konstruktor->getParameters() as $parameter) {
            $argumente[] = $this->loeseParameter($parameter, $klasse);
        }

        return $spiegel->newInstanceArgs($argumente);
    }

    /**
     * Löst einen einzelnen Konstruktor-Parameter auf: einen Klassen-
     * oder Interface-Typ holt der Container rekursiv, ein Parameter mit
     * Standardwert bekommt diesen. Alles andere überfordert das kleine
     * Autowiring - dann ist eine Fabrik gefragt.
     */
    private function loeseParameter(ReflectionParameter $parameter, string $klasse): mixed
    {
        $typ = $parameter->getType();

        // Ein Klassen-/Interface-Typ (kein int, string ...): rekursiv holen.
        if ($typ instanceof ReflectionNamedType && !$typ->isBuiltin()) {
            try {
                return $this->get($typ->getName());
            } catch (NotFoundExceptionInterface $fehlt) {
                // Wichtig für PSR-11: Der äußere Bezeichner ($klasse) wurde
                // über has() als vorhanden gemeldet. Ein fehlendes, weil
                // ungebundenes Interface darf darum kein NotFound nach außen
                // tragen - es ist ein Baufehler, also eine ContainerException.
                throw new ContainerException(sprintf(
                    'Abhängigkeit %s von %s ist nicht gebunden; bitte eine Fabrik mit set() registrieren.',
                    $typ->getName(),
                    $klasse,
                ), previous: $fehlt);
            }
        }

        // Skalarer Parameter mit Standardwert: den Standardwert nehmen.
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        // Ein skalarer Pflichtparameter (etwa eine DSN oder ein Schlüssel)
        // lässt sich nicht erraten - hier muss eine Fabrik einspringen.
        throw new ContainerException(sprintf(
            'Parameter $%s von %s lässt sich nicht automatisch auflösen; bitte eine Fabrik registrieren.',
            $parameter->getName(),
            $klasse,
        ));
    }
}
