<?php

declare(strict_types=1);

namespace Tests\Http\Data\Concerns;

use BadMethodCallException;
use Mollie\Api\Http\Data\Concerns\Macroable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MacroableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        MacroableFixture::flushMacros();
    }

    protected function tearDown(): void
    {
        MacroableFixture::flushMacros();

        parent::tearDown();
    }

    #[Test]
    public function it_registers_and_invokes_a_static_macro(): void
    {
        MacroableFixture::macro('shout', fn (string $word): string => strtoupper($word).'!');

        $this->assertTrue(MacroableFixture::hasMacro('shout'));
        /** @phpstan-ignore staticMethod.notFound */
        $this->assertSame('HELLO!', MacroableFixture::shout('hello'));
    }

    #[Test]
    public function it_registers_and_invokes_an_instance_macro(): void
    {
        MacroableFixture::macro('doubled', function (): int {
            /** @var MacroableFixture $this */
            return $this->value * 2;
        });

        $fixture = new MacroableFixture(21);

        /** @phpstan-ignore method.notFound */
        $this->assertSame(42, $fixture->doubled());
    }

    #[Test]
    public function instance_macro_can_access_protected_state(): void
    {
        MacroableFixture::macro('secret', function (): string {
            /** @var MacroableFixture $this */
            /** @phpstan-ignore property.protected */
            return $this->hidden;
        });

        $fixture = new MacroableFixture(0, 'shh');

        /** @phpstan-ignore method.notFound */
        $this->assertSame('shh', $fixture->secret());
    }

    #[Test]
    public function flush_macros_removes_all_registered_macros(): void
    {
        MacroableFixture::macro('foo', fn (): string => 'bar');
        $this->assertTrue(MacroableFixture::hasMacro('foo'));

        MacroableFixture::flushMacros();

        $this->assertFalse(MacroableFixture::hasMacro('foo'));
    }

    #[Test]
    public function static_call_to_missing_macro_throws(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('does not exist');

        /** @phpstan-ignore staticMethod.notFound */
        MacroableFixture::nope();
    }

    #[Test]
    public function instance_call_to_missing_macro_throws(): void
    {
        $fixture = new MacroableFixture(1);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('does not exist');

        /** @phpstan-ignore-next-line */
        $fixture->nope();
    }

    #[Test]
    public function non_closure_callables_are_supported(): void
    {
        MacroableFixture::macro('strtolower', 'strtolower');

        /** @phpstan-ignore staticMethod.notFound */
        $this->assertSame('abc', MacroableFixture::strtolower('ABC'));
    }
}

final class MacroableFixture
{
    use Macroable;

    public function __construct(
        public int $value = 0,
        protected string $hidden = '',
    ) {
    }
}
