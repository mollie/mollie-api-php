<?php

namespace Tests\Utils;

use Mollie\Api\Utils\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function test_join()
    {
        $baseUrl = 'https://example.com';
        $endpoint = '/api/v1/users';

        $expected = 'https://example.com/api/v1/users';
        $result = Url::join($baseUrl, $endpoint);

        $this->assertEquals($expected, $result);
    }

    public function test_is_valid()
    {
        $validUrl = 'https://example.com';
        $invalidUrl = 'example.com';

        $this->assertTrue(Url::isValid($validUrl));
        $this->assertFalse(Url::isValid($invalidUrl));
    }

    public function test_parse_query()
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
