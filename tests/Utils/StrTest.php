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
}
