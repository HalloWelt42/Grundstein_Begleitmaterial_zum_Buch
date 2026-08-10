<?php

declare(strict_types=1);

namespace Grundstein\Mini;

/**
 * Das Ergebnis eines Routenvergleichs kann nur eines von drei Dingen sein.
 * Ein Enum macht diese drei Fälle explizit und tippsicher - man kann keinen
 * vierten Zustand aus Versehen erfinden.
 */
enum RouteStatus
{
    /** Pfad und Methode passen: Es gibt einen Handler. */
    case Found;

    /** Kein Pfad passt: Antwort wird eine 404. */
    case NotFound;

    /** Der Pfad passt, aber nicht mit dieser Methode: Antwort wird eine 405. */
    case MethodNotAllowed;
}
