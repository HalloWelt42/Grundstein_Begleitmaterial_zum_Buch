<?php declare(strict_types=1);

/**
 * Kurzform für die sichere HTML-Ausgabe. Auch ein Suchbegriff aus
 * $_GET muss escaped werden, bevor er ins HTML kommt - GET macht eine
 * Eingabe nicht weniger gefährlich als POST.
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Eine Suche liest nur, sie verändert nichts - also GET.
$begriff = trim((string) ($_GET['q'] ?? ''));
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Suche</title>
</head>
<body>
    <form method="get" action="suche.php">
        <input type="text" name="q" value="<?= e($begriff) ?>">
        <button type="submit">Suchen</button>
    </form>
<?php if ($begriff !== ''): ?>
    <p>Du hast nach <?= e($begriff) ?> gesucht.</p>
<?php endif; ?>
</body>
</html>
