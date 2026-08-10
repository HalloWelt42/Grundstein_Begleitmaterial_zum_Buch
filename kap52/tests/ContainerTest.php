<?php

declare(strict_types=1);

namespace App\Tests;

use App\Container;
use App\Logger;
use App\Mailer;
use App\ProtokollMailer;
use App\Registrierung;
use App\SammelLogger;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

final class ContainerTest extends TestCase
{
    #[Test]
    public function wirft_not_found_bei_unbekanntem_bezeichner(): void
    {
        $container = new Container();

        // PSR-11 verlangt genau diese Ausnahme für einen fehlenden Eintrag.
        $this->expectException(NotFoundExceptionInterface::class);

        $container->get('gibt.es.nicht');
    }

    #[Test]
    public function has_meldet_bekannte_und_unbekannte_eintraege(): void
    {
        $container = new Container();
        $container->set('konfig', fn(): array => ['sprache' => 'de']);

        self::assertTrue($container->has('konfig'));            // registriert
        self::assertTrue($container->has(SammelLogger::class)); // Klasse -> autowirebar
        self::assertFalse($container->has('gibt.es.nicht'));    // nichts davon
    }

    #[Test]
    public function liefert_bei_mehrfachem_get_dieselbe_instanz(): void
    {
        $container = new Container();
        $container->set(SammelLogger::class, fn(): SammelLogger => new SammelLogger());

        // Singleton-Verhalten: einmal gebaut, danach geteilt.
        self::assertSame(
            $container->get(SammelLogger::class),
            $container->get(SammelLogger::class),
        );
    }

    #[Test]
    public function baut_den_dienst_erst_beim_ersten_get(): void
    {
        $container = new Container();

        $gebaut = false;
        $container->set('spaet', function () use (&$gebaut): stdClass {
            $gebaut = true;

            return new stdClass();
        });

        // Nur registriert - die Fabrik lief noch nicht.
        self::assertFalse($gebaut);

        $container->get('spaet');

        // Erst get() ruft die Fabrik auf (verzögert).
        self::assertTrue($gebaut);
    }

    #[Test]
    public function verdrahtet_einen_ganzen_objektgraphen(): void
    {
        $container = new Container();
        $container->set(Logger::class, fn(): Logger => new SammelLogger());
        $container->set(Mailer::class, fn(Container $c): Mailer => new ProtokollMailer(
            $c->get(Logger::class),
        ));
        $container->set(Registrierung::class, fn(Container $c): Registrierung => new Registrierung(
            $c->get(Mailer::class),
            $c->get(Logger::class),
        ));

        $container->get(Registrierung::class)->registriere('ada@example.org');

        // Dienst UND Mailer teilen sich denselben Logger, darum liegen
        // beide Zeilen in ein und derselben Instanz beisammen.
        $logger = $container->get(Logger::class);
        self::assertInstanceOf(SammelLogger::class, $logger);
        self::assertSame(
            [
                'Registrierung: ada@example.org',
                'Mail an ada@example.org: Willkommen',
            ],
            $logger->alleZeilen(),
        );
    }

    #[Test]
    public function autowiring_baut_den_graphen_aus_nur_zwei_bindungen(): void
    {
        $container = new Container();
        // Nur die Interface-Bindungen, alles Konkrete macht Autowiring.
        $container->set(Logger::class, fn(Container $c): Logger => $c->get(SammelLogger::class));
        $container->set(Mailer::class, fn(Container $c): Mailer => $c->get(ProtokollMailer::class));

        // Registrierung ist nirgends registriert und wird trotzdem gebaut.
        $registrierung = $container->get(Registrierung::class);
        self::assertInstanceOf(Registrierung::class, $registrierung);

        $registrierung->registriere('grace@example.org');

        $logger = $container->get(SammelLogger::class);
        self::assertInstanceOf(SammelLogger::class, $logger);
        self::assertCount(2, $logger->alleZeilen());
    }

    #[Test]
    public function autowiring_scheitert_ohne_interface_bindung_mit_container_exception(): void
    {
        $container = new Container();

        // has() meldet true, weil ProtokollMailer eine existierende Klasse
        // ist. Damit greift die PSR-11-Zusicherung: get() darf jetzt kein
        // "nicht gefunden" mehr werfen.
        self::assertTrue($container->has(ProtokollMailer::class));

        try {
            // ProtokollMailer braucht einen Logger (Interface). Ohne Bindung
            // kann der Container nicht wissen, welche Umsetzung er nehmen soll.
            $container->get(ProtokollMailer::class);
            self::fail('Erwartet wurde eine ContainerException.');
        } catch (NotFoundExceptionInterface) {
            // Das wäre der PSR-11-Bruch: bei has() == true nie ein NotFound.
            self::fail('get() darf bei has() == true kein NotFound werfen.');
        } catch (ContainerExceptionInterface $fehler) {
            // Richtig: der Eintrag ist bekannt, nur das Bauen scheitert.
            self::assertStringContainsString('Logger', $fehler->getMessage());
        }
    }

    #[Test]
    public function erkennt_eine_zyklische_abhaengigkeit(): void
    {
        $container = new Container();
        $container->set('a', fn(Container $c): mixed => $c->get('b'));
        $container->set('b', fn(Container $c): mixed => $c->get('a'));

        $this->expectException(ContainerExceptionInterface::class);

        $container->get('a');
    }
}
