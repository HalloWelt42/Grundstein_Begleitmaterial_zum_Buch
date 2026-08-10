<?php

declare(strict_types=1);

// Der Speicherort liegt außerhalb von public - erreichbar ist er nur
// über dieses Skript, das jede Anfrage kontrolliert ausliefert.
$zielordner = __DIR__ . '/../hochgeladen';

// Den gewünschten Namen lesen und mit basename() auf den reinen
// Dateinamen reduzieren. So kann niemand über "../" aus dem Ordner
// ausbrechen (Path Traversal).
$name = basename((string) ($_GET['datei'] ?? ''));
$pfad = $zielordner . '/' . $name;

// Nur ausliefern, was wirklich als Datei in diesem Ordner liegt.
if ($name === '' || !is_file($pfad)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Datei nicht gefunden.';
    exit;
}

// Den Typ aus dem Inhalt bestimmen und als Download anbieten. Die
// Kopfzeile Content-Disposition: attachment verhindert, dass der
// Browser die Datei im Seitenkontext interpretiert.
$typ = (new finfo(FILEINFO_MIME_TYPE))->file($pfad) ?: 'application/octet-stream';
header('Content-Type: ' . $typ);
header('Content-Disposition: attachment; filename="' . $name . '"');
header('Content-Length: ' . (string) filesize($pfad));
readfile($pfad);
