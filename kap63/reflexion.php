<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 63: Reflection und Attribute
 *
 * Ein Programm betrachtet sich selbst. Diese Datei definiert eine kleine
 * Klasse und liest danach mit Reflection ihren Aufbau aus - Eigenschaften,
 * ihre Typen und den Konstruktor -, ohne ein einziges Feld fest zu kennen.
 *
 * Start:  docker run --rm -v "$PWD":/app -w /app php:8.4-cli php reflexion.php
 */

final class Kunde
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $telefon = null,
    ) {}

    public function anzeigename(): string
    {
        return "#{$this->id} {$this->name}";
    }
}

/**
 * Formt einen Reflection-Typ in eine lesbare Kurzform: "?string", "int".
 * Ist kein Typ angegeben, liefert die Funktion "mixed".
 */
function typAlsText(?ReflectionNamedType $typ): string
{
    if ($typ === null) {
        return 'mixed';
    }

    return ($typ->allowsNull() ? '?' : '') . $typ->getName();
}

$spiegel = new ReflectionClass(Kunde::class);

echo 'Klasse: ' . $spiegel->getName()
    . ' (final: ' . ($spiegel->isFinal() ? 'ja' : 'nein') . ')' . PHP_EOL;

echo PHP_EOL . 'Eigenschaften:' . PHP_EOL;
foreach ($spiegel->getProperties() as $eigenschaft) {
    /** @var ReflectionProperty $eigenschaft */
    $typ = $eigenschaft->getType();
    $zusatz = $eigenschaft->isReadonly() ? ' (readonly)' : '';

    printf(
        "  %-8s \$%s%s%s",
        typAlsText($typ instanceof ReflectionNamedType ? $typ : null),
        $eigenschaft->getName(),
        $zusatz,
        PHP_EOL,
    );
}

echo PHP_EOL . 'Konstruktor-Parameter:' . PHP_EOL;
foreach ($spiegel->getConstructor()->getParameters() as $parameter) {
    $typ = $parameter->getType();
    $default = '';
    if ($parameter->isDefaultValueAvailable()) {
        $wert = $parameter->getDefaultValue();
        // null als Kleinschreibung zeigen, Zeichenketten in Anführungszeichen.
        $default = ' = ' . ($wert === null ? 'null' : var_export($wert, true));
    }

    printf(
        "  %-8s \$%s%s%s",
        typAlsText($typ instanceof ReflectionNamedType ? $typ : null),
        $parameter->getName(),
        $default,
        PHP_EOL,
    );
}

echo PHP_EOL . 'Öffentliche Methoden:' . PHP_EOL;
foreach ($spiegel->getMethods() as $methode) {
    if ($methode->isConstructor() || !$methode->isPublic()) {
        continue;
    }

    $rueckgabe = $methode->getReturnType();
    printf(
        "  %s(): %s%s",
        $methode->getName(),
        typAlsText($rueckgabe instanceof ReflectionNamedType ? $rueckgabe : null),
        PHP_EOL,
    );
}
