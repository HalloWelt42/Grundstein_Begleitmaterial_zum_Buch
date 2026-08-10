<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 69: PHP 8.5 im Detail
 *
 * Die URI-Erweiterung zerlegt und baut Adressen standardkonform nach
 * RFC 3986 - kein selbstgebautes Muster mehr, das an Sonderfällen
 * zerbricht. Die Erweiterung ist im Standard-Abbild php:8.5-cli enthalten.
 */

use Uri\Rfc3986\Uri;

$uri = new Uri(
    'https://ada:geheim@shop.example.org:8443/artikel/42?farbe=rot&groesse=m#details',
);

echo 'Schema:   ' . $uri->getScheme() . PHP_EOL;
echo 'Host:     ' . $uri->getHost() . PHP_EOL;
echo 'Port:     ' . $uri->getPort() . PHP_EOL;
echo 'Pfad:     ' . $uri->getPath() . PHP_EOL;
echo 'Query:    ' . $uri->getQuery() . PHP_EOL;
echo 'Fragment: ' . $uri->getFragment() . PHP_EOL;

echo PHP_EOL;

// Unveränderlich ändern: jede with*()-Methode liefert ein neues Objekt
// zurück und lässt das Original in Ruhe - dieselbe Haltung wie bei
// DateTimeImmutable aus Kapitel 66.
$neu = $uri
    ->withQuery('farbe=blau')
    ->withFragment('lieferung');

echo 'Original:  ' . $uri->toString() . PHP_EOL;
echo 'Geändert:  ' . $neu->toString() . PHP_EOL;

echo PHP_EOL;

// Eine relative Referenz gegen eine Basis auflösen.
$basis = new Uri('https://example.org/shop/katalog/');
$ziel = $basis->resolve('../hilfe/faq.html');

echo 'Basis:      ' . $basis->toString() . PHP_EOL;
echo 'Aufgelöst:  ' . $ziel->toString() . PHP_EOL;
