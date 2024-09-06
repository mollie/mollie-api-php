<?php

namespace Tests\Helpers;

use Mollie\Api\Helpers\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function testJoin()
    {
        $baseUrl = 'https://example.com';
        $endpoint = '/api/v1/users';

        $expected = 'https://example.com/api/v1/users';
        $result = Url::join($baseUrl, $endpoint);

        $this->assertEquals($expected, $result);
    }

    public function testIsValid()
    {
        $validUrl = 'https://example.com';
        $invalidUrl = 'example.com';

        $this->assertTrue(Url::isValid($validUrl));
        $this->assertFalse(Url::isValid($invalidUrl));
    }

    public function testParseQuery()
    {
        $query = 'param1=value1&param2=value2';

        $expected = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];
        $result = Url::parseQuery($query);

        $this->assertEquals($expected, $result);
    }
}
