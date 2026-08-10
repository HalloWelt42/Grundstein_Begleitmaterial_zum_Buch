<?php

declare(strict_types=1);

namespace Grundstein\Api;

use RuntimeException;

/**
 * Die gemeinsame Wurzel aller Fehler, die eine API-Antwort auslösen. Jede
 * konkrete Ausnahme trägt den passenden HTTP-Statuscode und einen kurzen,
 * maschinenlesbaren Fehlercode bei sich. So entsteht die Antwort an genau
 * einer Stelle - in der JsonErrorMiddleware -, und der Rest des Codes wirft
 * nur noch die passende Ausnahme.
 */
abstract class ApiException extends RuntimeException
{
    /** Der HTTP-Statuscode, mit dem diese Ausnahme beantwortet wird. */
    abstract public function status(): int;

    /** Der kurze, maschinenlesbare Fehlercode, etwa "not_found". */
    abstract public function errorCode(): string;

    /**
     * Zusätzliche Detailfelder für die Antwort - etwa die einzelnen
     * Feldfehler einer Validierung. Standardmässig leer.
     *
     * @return array<string, string>
     */
    public function details(): array
    {
        return [];
    }

    /**
     * Zusätzliche Kopfzeilen für die Antwort - etwa der Allow-Header bei
     * einer nicht erlaubten Methode. Standardmässig keine.
     *
     * @return array<string, string>
     */
    public function headers(): array
    {
        return [];
    }
}
