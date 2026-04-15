<?php

declare(strict_types=1);

namespace Tests\Http\Data;

use BadMethodCallException;
use Mollie\Api\Http\Data\Money;
use PHPUnit\Framework\TestCase;

final class MoneyMacroableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Money::flushMacros();
    }

    protected function tearDown(): void
    {
        Money::flushMacros();

        parent::tearDown();
    }

    /** @test */
    public function it_supports_custom_static_factories_via_macro(): void
    {
        Money::macro('fromCents', fn (int $cents): Money => new Money(
            currency: 'EUR',
            value: number_format($cents / 100, 2, '.', ''),
        ));

        $money = Money::fromCents(1234);

        $this->assertSame('EUR', $money->currency);
        $this->assertSame('12.34', $money->value);
    }

    /** @test */
    public function unknown_method_throws_bad_method_call_exception(): void
    {
        $this->expectException(BadMethodCallException::class);

        Money::nope('x');
    }
}
