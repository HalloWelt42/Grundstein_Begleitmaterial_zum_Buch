<?php

declare(strict_types=1);

use App\KeinRabatt;
use App\Position;
use App\ProzentRabatt;
use App\Warenkorb;

// Vor jedem Test einen frischen, leeren Warenkorb bereitstellen.
// $this ist in Pest der laufende Testfall - dort legen wir ihn ab.
beforeEach(function (): void {
    $this->korb = new Warenkorb();
});

test('ein neuer Warenkorb ist leer', function (): void {
    expect($this->korb->istLeer())->toBeTrue();
});

it('zählt die gelegten Posten', function (): void {
    $this->korb->lege(new Position('Tastatur', 5000, 1));
    $this->korb->lege(new Position('Maus', 2500, 1));

    // Zwei Erwartungen verkettet: ->and() hängt die nächste an.
    expect($this->korb->posten())->toHaveCount(2)
        ->and($this->korb->istLeer())->toBeFalse();
});

it('summiert Einzelpreis mal Menge', function (): void {
    $this->korb->lege(new Position('Kabel', 999, 3));

    expect($this->korb->zwischensumme())->toBe(2997);
});

it('zieht einen Prozentrabatt vom Endbetrag ab', function (): void {
    $korb = new Warenkorb(new ProzentRabatt(20));
    $korb->lege(new Position('Monitor', 20000, 1));

    expect($korb->endbetrag())->toBe(16000);
});

it('rechnet ohne Rabatt mit dem vollen Betrag', function (): void {
    $korb = new Warenkorb(new KeinRabatt());
    $korb->lege(new Position('Buch', 2999, 1));

    expect($korb->endbetrag())->toBe(2999);
});

// Datensatz-Test: derselbe Ablauf für viele Prozentsätze.
it('zieht den passenden Betrag ab', function (int $prozent, int $abzug): void {
    $korb = new Warenkorb(new ProzentRabatt($prozent));
    $korb->lege(new Position('Ware', 10000, 1));

    expect($korb->endbetrag())->toBe(10000 - $abzug);
})->with('rabattsaetze');

// Ausnahme prüfen: ein unsinniger Prozentsatz wird abgewiesen.
it('weist einen Prozentsatz über 100 ab', function (): void {
    expect(fn () => new ProzentRabatt(120))
        ->toThrow(InvalidArgumentException::class);
});
