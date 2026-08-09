<?php

declare(strict_types=1);

/**
 * Modelliert eine Ressource, die beim Erzeugen geöffnet und beim
 * Aufräumen wieder geschlossen werden muss. __construct und __destruct
 * markieren Anfang und Ende des Lebens eines Objekts.
 */
final class Verbindung
{
    public function __construct(
        private readonly string $name,
    ) {
        echo "Verbindung zu {$this->name} geöffnet.\n";
    }

    /**
     * Wird automatisch aufgerufen, wenn das Objekt nicht mehr gebraucht
     * wird - spätestens am Skriptende, früher bei unset().
     */
    public function __destruct()
    {
        echo "Verbindung zu {$this->name} geschlossen.\n";
    }

    public function frage(string $sql): void
    {
        echo "  Frage an {$this->name}: {$sql}\n";
    }
}

$db = new Verbindung('Katalog');
$db->frage('SELECT * FROM produkte');
echo "Arbeit erledigt.\n";

// unset() gibt das Objekt frei - der Destruktor läuft sofort.
unset($db);
echo "Nach dem Aufräumen.\n";
