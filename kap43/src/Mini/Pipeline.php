<?php

declare(strict_types=1);

namespace Grundstein\Mini;

use Grundstein\Http\Request;
use Grundstein\Http\Response;

/**
 * Die Pipeline reiht Middleware vor einen Kern-Handler (hier den
 * Dispatcher). Sie ist selbst ein RequestHandler - eine Pipeline kann also
 * wieder in einer Pipeline stecken.
 *
 * Der Bau der Kette geschieht von hinten nach vorne: Der Kern wird zuerst
 * von der letzten Middleware umwickelt, dann von der vorletzten, und so
 * fort. So läuft eine Anfrage von der ersten zur letzten Middleware in den
 * Kern und die Antwort denselben Weg zurück - wie Zwiebelschalen.
 */
final class Pipeline implements RequestHandler
{
    /**
     * @param list<Middleware> $middleware in Aufrufreihenfolge
     */
    public function __construct(
        private readonly array $middleware,
        private readonly RequestHandler $kern,
    ) {
    }

    public function handle(Request $request): Response
    {
        $naechster = $this->kern;

        foreach (array_reverse($this->middleware) as $mw) {
            // Jede Middleware wird in einen kleinen RequestHandler verpackt,
            // der ihren process()-Aufruf an den bisher gebauten Rest reicht.
            $naechster = new class($mw, $naechster) implements RequestHandler {
                public function __construct(
                    private readonly Middleware $mw,
                    private readonly RequestHandler $next,
                ) {
                }

                public function handle(Request $request): Response
                {
                    return $this->mw->process($request, $this->next);
                }
            };
        }

        return $naechster->handle($request);
    }
}
