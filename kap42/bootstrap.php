<?php

declare(strict_types=1);

// Ein winziger Lader, der alle Klassen des Beispiels einbindet. In einem
// echten Projekt übernimmt das der Autoloader von Composer (Kapitel 21);
// hier reichen ein paar require-Zeilen, damit das Beispiel ohne weitere
// Einrichtung läuft.
require __DIR__ . '/src/Message.php';
require __DIR__ . '/src/ServerRequest.php';
require __DIR__ . '/src/Response.php';
require __DIR__ . '/src/RequestHandler.php';
require __DIR__ . '/src/Middleware.php';
require __DIR__ . '/src/Pipeline.php';
require __DIR__ . '/src/Protokoll.php';
require __DIR__ . '/src/LoggingMiddleware.php';
require __DIR__ . '/src/AuthMiddleware.php';
require __DIR__ . '/src/BegruessungsHandler.php';
