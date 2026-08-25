<?php

declare(strict_types=1);

namespace Tests\Factories;

use Mollie\Api\Factories\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    #[Test]
    #[DataProvider('falsyValues')]
    public function get_returns_present_falsy_values($value)
    {
        $factory = $this->factory(['value' => $value]);

        $this->assertSame($value, $factory->value('value', 'default'));
    }

    public static function falsyValues(): iterable
    {
        yield 'false' => [false];
        yield 'integer zero' => [0];
        yield 'string zero' => ['0'];
        yield 'empty string' => [''];
        yield 'empty array' => [[]];
        yield 'null' => [null];
    }

    #[Test]
    public function get_returns_default_when_key_is_missing()
    {
        $factory = $this->factory([]);

        $this->assertSame('default', $factory->value('missing', 'default'));
    }

    #[Test]
    public function get_prefers_falsy_primary_value_over_truthy_backup()
    {
        $factory = $this->factory([
            'enabled' => false,
            'filters' => ['enabled' => true],
        ]);

        $this->assertFalse($factory->value('enabled'));
    }

    #[Test]
    public function get_uses_backup_when_primary_key_is_missing()
    {
        $factory = $this->factory([
            'filters' => ['enabled' => true],
        ]);

        $this->assertTrue($factory->value('enabled', 'default'));
    }

    #[Test]
    public function get_respects_ordered_array_key_precedence()
    {
        $factory = $this->factory([
            'first' => false,
            'second' => 'fallback',
        ]);

        $this->assertFalse($factory->value(['first', 'second'], null, null, null));
    }

    private function factory(array $data): object
    {
        return new class($data) extends Factory {
            public function value($key = null, $default = null, $data = null, $backupKey = 'filters.')
            {
                return $this->get($key, $default, $data, $backupKey);
            }
        };
    }
}
