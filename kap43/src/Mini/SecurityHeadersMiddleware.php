<?php

declare(strict_types=1);

namespace Grundstein\Mini;

use Grundstein\Http\Request;
use Grundstein\Http\Response;

/**
 * Eine Beispiel-Middleware: Sie lässt den Rest der Kette antworten und
 * ergänzt danach zwei Sicherheits-Header auf jeder Antwort. Genau das ist
 * die Stärke der Pipeline - ein Belang, der jede Route betrifft, steht an
 * einer Stelle statt in jedem Handler.
 */
final class SecurityHeadersMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $next): Response
    {
        // Erst den Rest der Kette arbeiten lassen ...
        $response = $next->handle($request);

        // ... dann die fertige Antwort um Header ergänzen.
        return $response
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
