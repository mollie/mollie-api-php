<?php

declare(strict_types=1);

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\MoneyBuilder;

it('creates EUR money from minor units', function (): void {
    $money = Money::of('EUR')->minorUnits(1000);

    expect($money)->toBeInstanceOf(Money::class)
        ->and($money->currency)->toBe('EUR')
        ->and($money->value)->toBe('10.00');
});

it('uppercases zero-decimal currencies when creating from minor units', function (): void {
    $money = Money::of('jpy')->minorUnits(1000);

    expect($money->currency)->toBe('JPY')
        ->and($money->value)->toBe('1000');
});

it('creates three-decimal currency money from minor units', function (): void {
    $money = Money::of('BHD')->minorUnits(1000);

    expect($money->currency)->toBe('BHD')
        ->and($money->value)->toBe('1.000');
});

it('supports negative minor unit amounts', function (): void {
    $money = Money::of('EUR')->minorUnits(-1000);

    expect($money->currency)->toBe('EUR')
        ->and($money->value)->toBe('-10.00');
});

it('supports zero minor unit amounts', function (): void {
    $money = Money::of('EUR')->minorUnits(0);

    expect($money->currency)->toBe('EUR')
        ->and($money->value)->toBe('0.00');
});

it('creates money from a string value', function (): void {
    $money = Money::of('EUR')->fromString('10.00');

    expect($money->currency)->toBe('EUR')
        ->and($money->value)->toBe('10.00');
});

it('uppercases currency when creating from a string value', function (): void {
    $money = Money::of('eur')->fromString('10.00');

    expect($money->currency)->toBe('EUR')
        ->and($money->value)->toBe('10.00');
});

it('returns a readonly builder', function (): void {
    $reflection = new ReflectionClass(MoneyBuilder::class);

    expect($reflection->isReadOnly())->toBeTrue();
});

it('keeps existing money construction paths working', function (): void {
    $constructed = new Money('EUR', '10.00');
    $fromMinorUnits = Money::fromMinorUnits('EUR', 1000);

    expect($constructed->currency)->toBe('EUR')
        ->and($constructed->value)->toBe('10.00')
        ->and($fromMinorUnits->currency)->toBe('EUR')
        ->and($fromMinorUnits->value)->toBe('10.00');
});
