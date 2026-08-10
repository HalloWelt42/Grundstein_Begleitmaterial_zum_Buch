<?php

declare(strict_types=1);

namespace Grundstein\Api;

use Grundstein\Http\Request;
use Grundstein\Http\Response;
use Grundstein\Mini\Middleware;
use Grundstein\Mini\RequestHandler;

/**
 * Die eine Stelle, an der aus einem Fehler eine Antwort wird. Sie lässt den
 * Rest der Kette arbeiten und fängt jede ApiException ab, die dabei
 * hochblubbert - vom Routing wie aus einem Controller. Aus ihr baut sie das
 * einheitliche Fehlerformat: ein Objekt unter dem Schlüssel "error" mit
 * code und message, bei Bedarf ergänzt um details.
 *
 * Weil das Format an genau einer Stelle entsteht, sieht jeder Fehler der
 * API gleich aus - egal, wo er ausgelöst wurde.
 */
final class JsonErrorMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $next): Response
    {
        try {
            return $next->handle($request);
        } catch (ApiException $e) {
            // Der Kern des einheitlichen Formats: code und message immer,
            // details nur, wenn die Ausnahme welche mitbringt.
            $fehler = [
                'code' => $e->errorCode(),
                'message' => $e->getMessage(),
            ];

            $details = $e->details();
            if ($details !== []) {
                $fehler['details'] = $details;
            }

            $response = (new Response())
                ->status($e->status())
                ->json(['error' => $fehler]);

            // Zusatz-Header der Ausnahme übernehmen, etwa Allow bei 405.
            foreach ($e->headers() as $name => $wert) {
                $response->header($name, $wert);
            }

            return $response;
        }
    }
}
