<?php

declare(strict_types=1);

namespace App;

use ReflectionClass;

/**
 * Ein schmaler Helfer um die beiden Reflection-Aufrufe, mit denen PHP 8.4
 * Lazy Objects erzeugt. Er kapselt die immer gleiche Zeremonie und macht
 * an der Aufrufstelle sichtbar, welche Spielart gemeint ist.
 */
final class LazyFabrik
{
    /**
     * Baut einen Lazy-Ghost: dasselbe Objekt füllt sich beim ersten
     * Zugriff selbst - meist, indem der Initialisierer seinen Konstruktor
     * aufruft.
     *
     * @template T of object
     * @param class-string<T>  $klasse
     * @param callable(T): void $initialisierer
     * @return T
     */
    public static function ghost(string $klasse, callable $initialisierer): object
    {
        return (new ReflectionClass($klasse))->newLazyGhost($initialisierer);
    }

    /**
     * Baut einen Lazy-Proxy: ein Platzhalter verweist beim ersten Zugriff
     * auf das echte Objekt, das die Fabrik dann baut und zurückgibt.
     *
     * @template T of object
     * @param class-string<T>  $klasse
     * @param callable(T): T   $fabrik
     * @return T
     */
    public static function proxy(string $klasse, callable $fabrik): object
    {
        return (new ReflectionClass($klasse))->newLazyProxy($fabrik);
    }
}
