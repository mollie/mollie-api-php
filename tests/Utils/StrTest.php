<?php

namespace Tests\Utils;

use Mollie\Api\Utils\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    /**
     * @test
     * @dataProvider lowerProvider
     */
    public function lower(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::lower($input));
    }

    public function lowerProvider(): array
    {
        return [
            'ascii' => ['FooBAR', 'foobar'],
            'digits and ascii' => ['123-ABC', '123-abc'],
            'multibyte' => ['ÄÖÜ Ç', 'äöü ç'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider kebabProvider
     */
    public function kebab(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::kebab($input));
    }

    public function kebabProvider(): array
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

    /**
     * @test
     * @dataProvider beforeProvider
     */
    public function before(string $subject, string $search, string $expected): void
    {
        $this->assertSame($expected, Str::before($subject, $search));
    }

    public function beforeProvider(): array
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

    /**
     * @test
     * @dataProvider snakeProvider
     */
    public function snake(string $input, string $expected, ?string $delimiter = null): void
    {
        $this->assertSame(
            $expected,
            $delimiter === null ? Str::snake($input) : Str::snake($input, $delimiter)
        );
    }

    public function snakeProvider(): array
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
}
