<?php

declare(strict_types=1);

namespace Tests\Utils;

use Mollie\Api\Utils\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    #[DataProvider('lowerProvider')]
    #[Test]
    public function lower(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::lower($input));
    }

    public static function lowerProvider(): array
    {
        return [
            'ascii' => ['FooBAR', 'foobar'],
            'digits and ascii' => ['123-ABC', '123-abc'],
            'multibyte' => ['ÄÖÜ Ç', 'äöü ç'],
        ];
    }

    #[DataProvider('kebabProvider')]
    #[Test]
    public function kebab(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::kebab($input));
    }

    public static function kebabProvider(): array
    {
        return [
            'pascal case' => ['FooBar', 'foo-bar'],
            'camel case' => ['fooBarBaz', 'foo-bar-baz'],
            'snake case' => ['foo_bar', 'foo-bar'],
            'mixed underscores' => ['Foo__BarBaz', 'foo-bar-baz'],
            'already kebab' => ['already-kebab', 'already-kebab'],
            'trim leading trailing' => ['-LeadingTrailing-', 'leading-trailing'],
        ];
    }

    #[DataProvider('beforeProvider')]
    #[Test]
    public function before(string $subject, string $search, string $expected): void
    {
        $this->assertSame($expected, Str::before($subject, $search));
    }

    public static function beforeProvider(): array
    {
        return [
            'basic' => ['hello.world', '.', 'hello'],
            'no delimiter' => ['helloworld', '.', 'helloworld'],
            'empty search returns subject' => ['foo', '', 'foo'],
            'delimiter at start' => ['.start', '.', ''],
            'multiple occurrences returns before first' => ['a-b-c', '-', 'a'],
            'subject empty' => ['', '-', ''],
            'unicode' => ['über-cool', '-', 'über'],
        ];
    }

    #[DataProvider('snakeProvider')]
    #[Test]
    public function snake(string $input, string $expected, ?string $delimiter = null): void
    {
        $this->assertSame(
            $expected,
            $delimiter === null ? Str::snake($input) : Str::snake($input, $delimiter)
        );
    }

    public static function snakeProvider(): array
    {
        return [
            'kebab case' => ['foo-bar-baz', 'foo_bar_baz'],
            'camel case' => ['fooBarBaz', 'foo_bar_baz'],
            'pascal case' => ['FooBarBaz', 'foo_bar_baz'],
            'with spaces' => ['Foo Bar Baz', 'foo_bar_baz'],
            'already snake' => ['foo_bar_baz', 'foo_bar_baz'],
            'numbers' => ['Version2Number', 'version2_number'],
            'acronym sequence' => ['HTTPResponseCode', 'h_t_t_p_response_code'],
            'custom delimiter dash' => ['FooBar', 'foo-bar', '-'],
        ];
    }

    #[DataProvider('matchProvider')]
    #[Test]
    public function match(string $subject, $pattern, $expected): void
    {
        $result = Str::match($subject, $pattern);

        if ($expected === false) {
            $this->assertFalse($result);
        } else {
            $this->assertIsArray($result);
            $this->assertCount(count($expected), $result);
            foreach ($expected as $index => $value) {
                $this->assertSame($value, $result[$index]);
            }
        }
    }

    public static function matchProvider(): array
    {
        return [
            'simple match' => ['hello', '/^([a-z]+)$/', ['hello', 'hello']],
            'match with groups' => ['EUR 10.00', '/^([A-Z]{3})\s+([\d.]+)$/i', ['EUR 10.00', 'EUR', '10.00']],
            'match currency last' => ['10.00 EUR', '/^([\d.]+)\s+([A-Z]{3})$/i', ['10.00 EUR', '10.00', 'EUR']],
            'case insensitive match' => ['HELLO', '/^([a-z]+)$/i', ['HELLO', 'HELLO']],
            'no match returns false' => ['123', '/^([a-z]+)$/', false],
            'empty string no match' => ['', '/^([a-z]+)$/', false],
            'pattern with digits' => ['12345', '/^(\d+)$/', ['12345', '12345']],
            'pattern with word boundaries' => ['this is a word', '/\b(word)\b/', ['word', 'word']],
            'pattern with multiple groups' => ['2024-01-15', '/^(\d{4})-(\d{2})-(\d{2})$/', ['2024-01-15', '2024', '01', '15']],
            'unicode pattern' => ['café', '/^([\p{L}]+)$/u', ['café', 'café']],
        ];
    }
}