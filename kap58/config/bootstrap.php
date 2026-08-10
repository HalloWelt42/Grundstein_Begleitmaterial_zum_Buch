<?php

declare(strict_types=1);

use App\Application\BestellungAufgebenDienst;
use App\Application\BestellungAufgegeben;
use App\Application\Bestellungen;
use App\Http\BestellungController;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Config\UmgebungsdateiLeser;
use App\Infrastructure\Container\Container;
use App\Infrastructure\Events\EreignisVerteiler;
use App\Infrastructure\Events\ListenerRegister;
use App\Infrastructure\Listener\BestaetigungProtokollieren;
use App\Infrastructure\Persistence\PdoBestellungen;
use Psr\EventDispatcher\EventDispatcherInterface;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Die Kompositionswurzel (Kapitel 52). Dies ist der EINE Ort, an dem konkrete
 * Klassen mit new zusammengesteckt und Interfaces an ihre Umsetzung gebunden
 * werden. Sie liest die Umgebungsdatei, baut das typisierte Config-Objekt,
 * füllt den Container mit Fabriken und gibt ihn fertig zurück. Der ganze Rest
 * der Anwendung fragt nur noch fertige Dienste ab und kennt weder new noch
 * den Container selbst.
 *
 * Die Datei liefert eine Fabrik-Closure zurück, damit sich der Aufbau mit
 * verschiedenen Umgebungsdateien wiederholen lässt (Betrieb wie Test).
 */
return static function (?string $envPfad = null): Container {
    // 1. Konfiguration laden: erst die Umgebungsdatei, dann die echten
    //    Prozess-Umgebungsvariablen darüberlegen (diese haben Vorrang).
    $werte = (new UmgebungsdateiLeser())->lies($envPfad ?? '');

    foreach (['APP_NAME', 'APP_UMGEBUNG', 'DB_DSN'] as $schluessel) {
        $ausUmgebung = getenv($schluessel);
        if ($ausUmgebung !== false) {
            $werte[$schluessel] = $ausUmgebung;
        }
    }

    $config = Config::ausWerten($werte);

    // 2. Den Container aufbauen und mit Bauanleitungen füllen.
    $container = new Container();

    // Das Config-Objekt selbst ist im Container abrufbar.
    $container->set(Config::class, static fn (): Config => $config);

    // Die PDO-Verbindung entsteht aus der Konfiguration. In der Entwicklung
    // legt sie das Schema gleich selbst an (bei sqlite::memory: nötig, weil
    // jede Verbindung eine frische, leere Datenbank bekommt).
    $container->set(PDO::class, static function (Container $c): PDO {
        $config = $c->get(Config::class);

        $pdo = new PDO($config->datenbankDsn, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        if ($config->istEntwicklung()) {
            $schema = (string) file_get_contents(
                __DIR__ . '/../src/Infrastructure/Persistence/schema.sql'
            );
            $pdo->exec($schema);
        }

        return $pdo;
    });

    // Der Repository-Port wird an den PDO-Adapter gebunden. Nur diese eine
    // Zeile entscheidet, welche Technik hinter dem Port steckt.
    $container->set(
        Bestellungen::class,
        static fn (Container $c): Bestellungen => new PdoBestellungen($c->get(PDO::class)),
    );

    // Die Ereignis-Infrastruktur (PSR-14). Der Zuhörer wird geteilt, damit die
    // Demo die verschickten Bestätigungen später auslesen kann.
    $container->set(
        BestaetigungProtokollieren::class,
        static fn (): BestaetigungProtokollieren => new BestaetigungProtokollieren(),
    );

    $container->set(ListenerRegister::class, static function (Container $c): ListenerRegister {
        $register = new ListenerRegister();

        // Hier wird die Verbindung "Ereignis -> Reaktion" geknüpft.
        $register->hoerAuf(
            BestellungAufgegeben::class,
            $c->get(BestaetigungProtokollieren::class),
        );

        return $register;
    });

    $container->set(
        EventDispatcherInterface::class,
        static fn (Container $c): EventDispatcherInterface
            => new EreignisVerteiler($c->get(ListenerRegister::class)),
    );

    // Der Anwendungsdienst bekommt seine beiden Ports gereicht.
    $container->set(
        BestellungAufgebenDienst::class,
        static fn (Container $c): BestellungAufgebenDienst => new BestellungAufgebenDienst(
            $c->get(Bestellungen::class),
            $c->get(EventDispatcherInterface::class),
        ),
    );

    // Der treibende Adapter an der Spitze.
    $container->set(
        BestellungController::class,
        static fn (Container $c): BestellungController => new BestellungController(
            $c->get(BestellungAufgebenDienst::class),
        ),
    );

    return $container;
};
