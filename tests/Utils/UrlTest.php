<?php

declare(strict_types=1);

namespace Tests\Utils;

use Mollie\Api\Utils\Url;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    #[Test]
    public function join()
    {
        $baseUrl = 'https://example.com';
        $endpoint = '/api/v1/users';

        $expected = 'https://example.com/api/v1/users';
        $result = Url::join($baseUrl, $endpoint);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function join_encodes_relative_path_segments()
    {
        $result = Url::join('https://api.mollie.com/v2', 'payments/tr_x%2F..%2Forders/refunds/re id');

        $this->assertSame(
            'https://api.mollie.com/v2/payments/tr_x%252F..%252Forders/refunds/re%20id',
            $result
        );
    }

    #[Test]
    public function join_encodes_dot_only_segments()
    {
        $result = Url::join('https://api.mollie.com/v2', 'payments/tr_x/../orders');

        $this->assertSame('https://api.mollie.com/v2/payments/tr_x/%2E%2E/orders', $result);
    }

    #[Test]
    public function join_keeps_absolute_urls_unchanged()
    {
        $absoluteUrl = 'https://api.mollie.com/v2/payments/tr_x/../orders';

        $this->assertSame($absoluteUrl, Url::join('https://example.com', $absoluteUrl));
    }

    #[Test]
    public function is_valid()
    {
        $validUrl = 'https://example.com';
        $invalidUrl = 'example.com';

        $this->assertTrue(Url::isValid($validUrl));
        $this->assertFalse(Url::isValid($invalidUrl));
    }

    #[Test]
    public function parse_query()
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
