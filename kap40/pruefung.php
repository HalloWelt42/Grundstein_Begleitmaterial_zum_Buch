<?php

declare(strict_types=1);

/**
 * Erlaubte MIME-Typen und die dazu passende, sichere Dateiendung. Der
 * Schlüssel ist der tatsächliche Typ (später mit finfo ermittelt),
 * der Wert die Endung, die wir selbst vergeben - niemals die des Nutzers.
 *
 * @var array<string, string>
 */
const ERLAUBTE_TYPEN = [
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
    'image/gif'       => 'gif',
    'image/webp'      => 'webp',
    'application/pdf' => 'pdf',
];

/** Höchstgröße einer hochgeladenen Datei in Bytes (hier 2 MiB). */
const MAX_BYTES = 2 * 1024 * 1024;

/**
 * Übersetzt einen Fehlercode aus $_FILES['...']['error'] in eine
 * verständliche Meldung. Bei UPLOAD_ERR_OK (dem Wert 0) ist alles in
 * Ordnung, und es kommt ein leerer String zurück.
 */
function fehlermeldung(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_OK         => '',
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE  => 'Die Datei ist zu groß.',
        UPLOAD_ERR_PARTIAL    => 'Die Datei wurde nur teilweise übertragen.',
        UPLOAD_ERR_NO_FILE    => 'Es wurde keine Datei ausgewählt.',
        UPLOAD_ERR_NO_TMP_DIR => 'Auf dem Server fehlt der Ordner für temporäre Dateien.',
        UPLOAD_ERR_CANT_WRITE => 'Die Datei konnte auf dem Server nicht abgelegt werden.',
        UPLOAD_ERR_EXTENSION  => 'Eine PHP-Erweiterung hat den Upload gestoppt.',
        default               => 'Beim Hochladen ist ein unbekannter Fehler aufgetreten.',
    };
}

/**
 * Ermittelt den echten MIME-Typ einer Datei anhand ihres Inhalts. finfo
 * liest die ersten Bytes (die sogenannte magische Zahl) und ist damit
 * fälschungssicherer als die vom Browser gemeldete Angabe oder die Endung.
 */
function echterTyp(string $pfad): string
{
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $typ = $finfo->file($pfad);

    return $typ === false ? '' : $typ;
}

/**
 * Erzeugt einen zufälligen, praktisch kollisionsfreien Dateinamen mit der
 * übergebenen Endung. random_bytes liefert kryptografisch sichere
 * Zufallsbytes; der Originalname des Nutzers wird nie verwendet.
 */
function zufallsname(string $endung): string
{
    return bin2hex(random_bytes(16)) . '.' . $endung;
}

/**
 * Prüft eine hochgeladene Datei in der richtigen Reihenfolge und liefert
 * das Ergebnis: eine Fehlermeldung (leer, wenn alles stimmt) und die
 * sichere Endung, die zum tatsächlichen Typ passt.
 *
 * @return array{fehler: string, endung: string}
 */
function pruefeUpload(int $fehlercode, int $groesse, string $tmpPfad): array
{
    // 1. Immer zuerst den Fehlercode auswerten. Ist er nicht UPLOAD_ERR_OK,
    //    steht in tmp_name keine brauchbare Datei, und alles Weitere wäre sinnlos.
    $meldung = fehlermeldung($fehlercode);
    if ($meldung !== '') {
        return ['fehler' => $meldung, 'endung' => ''];
    }

    // 2. Die Größe serverseitig begrenzen - unabhängig von den Grenzen
    //    in der php.ini, denen wir uns nicht ausliefern wollen.
    if ($groesse > MAX_BYTES) {
        return ['fehler' => 'Die Datei ist zu groß (höchstens 2 MiB).', 'endung' => ''];
    }

    // 3. Den echten Typ aus dem Inhalt lesen und gegen die Positivliste
    //    prüfen. Der vom Browser gemeldete Typ wird bewusst ignoriert.
    $typ = echterTyp($tmpPfad);
    if (!isset(ERLAUBTE_TYPEN[$typ])) {
        return ['fehler' => 'Dieser Dateityp ist nicht erlaubt.', 'endung' => ''];
    }

    return ['fehler' => '', 'endung' => ERLAUBTE_TYPEN[$typ]];
}
