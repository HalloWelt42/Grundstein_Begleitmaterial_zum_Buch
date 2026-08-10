<?php

declare(strict_types=1);

require __DIR__ . '/pruefung.php';

/**
 * Kurzform für die sichere HTML-Ausgabe. Jeder Wert, der aus einer
 * Nutzereingabe stammt - hier vor allem der Dateiname -, läuft durch
 * diese Funktion, bevor er ins HTML kommt.
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Zielordner für gespeicherte Dateien - bewusst außerhalb des
// öffentlichen Verzeichnisses (public), damit niemand eine hochgeladene
// Datei direkt über ihre URL aufrufen und womöglich ausführen lassen kann.
$zielordner = __DIR__ . '/../hochgeladen';

$meldung = '';
$erfolg = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fehlt der Schlüssel, hat der Browser gar kein Dateifeld geschickt.
    $datei = $_FILES['dokument'] ?? null;

    if ($datei === null) {
        $meldung = 'Es wurde keine Datei übermittelt.';
    } else {
        $ergebnis = pruefeUpload(
            (int) $datei['error'],
            (int) $datei['size'],
            (string) $datei['tmp_name'],
        );

        if ($ergebnis['fehler'] !== '') {
            $meldung = $ergebnis['fehler'];
        } elseif (!is_uploaded_file($datei['tmp_name'])) {
            // Schutz gegen untergeschobene Pfade: nur echte Uploads dürfen weiter.
            $meldung = 'Die Datei stammt nicht aus einem gültigen Upload.';
        } else {
            // Der Zielname ist rein zufällig, die Endung stammt aus der
            // Positivliste. Der Originalname wird nirgends verwendet.
            $zielname = zufallsname($ergebnis['endung']);
            $zielpfad = $zielordner . '/' . $zielname;

            if (move_uploaded_file($datei['tmp_name'], $zielpfad)) {
                $erfolg = true;
                $meldung = 'Die Datei wurde gespeichert als ' . $zielname;
            } else {
                $meldung = 'Die Datei konnte nicht gespeichert werden.';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Datei hochladen</title>
</head>
<body>
    <h1>Datei hochladen</h1>
    <form method="post" action="upload.php" enctype="multipart/form-data">
        <!-- PHP prüft diesen Wert selbst (löst UPLOAD_ERR_FORM_SIZE aus), doch der
             Client bestimmt ihn - kein Schutz. Die echte Grenze zieht der Server. -->
        <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
        <p>
            <label>Datei (JPEG, PNG, GIF, WebP oder PDF, höchstens 2 MiB):<br>
                <input type="file" name="dokument">
            </label>
        </p>
        <p>
            <button type="submit">Hochladen</button>
        </p>
    </form>
<?php if ($meldung !== ''): ?>
    <p class="<?= $erfolg ? 'ok' : 'fehler' ?>"><?= e($meldung) ?></p>
<?php endif; ?>
</body>
</html>
