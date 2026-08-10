<?php

declare(strict_types=1);

namespace Grundstein\Mini;

/**
 * Der Router hält die Tabelle aller Routen und beantwortet die eine Frage,
 * um die sich alles dreht: Welcher Handler gehört zu dieser Methode und
 * diesem Pfad? Muster wie "/kunden/{id}" werden beim Registrieren einmal
 * in einen regulären Ausdruck übersetzt; beim Vergleich fällt dann auch
 * der Wert des Platzhalters ab.
 */
final class Router
{
    /** @var list<Route> */
    private array $routes = [];

    /** Registriert eine Route für eine beliebige Methode. */
    public function add(string $method, string $muster, callable $handler): void
    {
        [$regex, $paramNames] = $this->kompiliere($muster);

        $this->routes[] = new Route($method, $regex, $paramNames, $handler);
    }

    /** Bequemer Kurzweg für GET-Routen. */
    public function get(string $muster, callable $handler): void
    {
        $this->add('GET', $muster, $handler);
    }

    /** Bequemer Kurzweg für POST-Routen. */
    public function post(string $muster, callable $handler): void
    {
        $this->add('POST', $muster, $handler);
    }

    /**
     * Vergleicht Methode und Pfad mit allen Routen. Passt der Pfad
     * mitsamt Methode, kommt Found samt Handler und Pfadparametern zurück.
     * Passt der Pfad, aber keine Methode, kommt MethodNotAllowed mit der
     * Liste der erlaubten Methoden. Passt gar nichts, kommt NotFound.
     */
    public function match(string $method, string $path): RouteResult
    {
        $erlaubt = [];

        foreach ($this->routes as $route) {
            if (preg_match($route->regex, $path, $treffer) !== 1) {
                continue;
            }

            // Der Pfad passt. Stimmt auch die Methode, ist es ein Treffer.
            if ($route->method === $method) {
                $params = [];
                foreach ($route->paramNames as $name) {
                    $params[$name] = $treffer[$name];
                }

                return new RouteResult(RouteStatus::Found, $route->handler, $params);
            }

            // Pfad passt, Methode nicht - für eine mögliche 405 merken.
            $erlaubt[] = $route->method;
        }

        if ($erlaubt !== []) {
            return new RouteResult(
                RouteStatus::MethodNotAllowed,
                allowedMethods: array_values(array_unique($erlaubt)),
            );
        }

        return new RouteResult(RouteStatus::NotFound);
    }

    /**
     * Übersetzt ein Muster in einen regulären Ausdruck. Aus "{id}" wird
     * eine benannte Gruppe, die alles bis zum nächsten Schrägstrich fängt.
     * Der übrige Text wird mit preg_quote entschärft, damit Sonderzeichen
     * im Pfad nicht als Regex-Metazeichen wirken.
     *
     * @return array{0: string, 1: list<string>} Regex und Parameternamen
     */
    private function kompiliere(string $muster): array
    {
        $paramNames = [];

        // Erst alles maskieren, dann die maskierten Platzhalter ersetzen.
        $maskiert = preg_quote($muster, '#');

        $regex = preg_replace_callback(
            '#\\\\\{(\w+)\\\\\}#',
            function (array $m) use (&$paramNames): string {
                $paramNames[] = $m[1];

                return '(?P<' . $m[1] . '>[^/]+)';
            },
            $maskiert,
        );

        return ['#^' . $regex . '$#', $paramNames];
    }
}
