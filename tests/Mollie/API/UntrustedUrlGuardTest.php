<?php

namespace Tests\Mollie\Api;

use Mollie\Api\Exceptions\UntrustedUrlException;
use Mollie\Api\HttpAdapter\MollieHttpAdapterInterface;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\Subscription;
use PHPUnit\Framework\TestCase;

class UntrustedUrlGuardTest extends TestCase
{
    /** @dataProvider untrustedUrls */
    public function testRejectsUntrustedUrlsBeforeCallingAdapter($url)
    {
        $adapter = $this->createMock(MollieHttpAdapterInterface::class);
        $adapter->expects($this->never())->method('send');
        $client = new MollieApiClient($adapter);
        $client->setApiKey('test_foobarfoobarfoobarfoobarfoobar');

        $this->expectException(UntrustedUrlException::class);
        $client->performHttpCallToFullUrl('GET', $url);
    }

    public static function untrustedUrls(): array
    {
        return array_map(function ($url) { return [$url]; }, [
            'https://attacker.example/v2/payments',
            'http://api.mollie.com/v2/payments',
            'https://api.mollie.com:8443/v2/payments',
            'https://api.mollie.com:0/v2/payments',
            'https://api.mollie.com@attacker.example/v2/payments',
            'https://api.mollie.com.attacker.example/v2/payments',
            'https://attacker.example#@api.mollie.com/',
            'https://api.mollie.com./v2/payments',
            'https://attacker:secret@api.mollie.com/v2/payments',
            'https://api.mollie.com:+443/v2/payments',
            'https://api.mollie.com\\@attacker.example/v2/payments',
            "https://api.mollie.com\n@attacker.example/v2/payments",
            'file:///etc/passwd',
            '//attacker.example/v2/payments',
            '/v2/payments',
        ]);
    }

    /** @dataProvider trustedUrls */
    public function testTrustedUrlsKeepAuthorizationAndStripFragments($url, $endpoint, $expected)
    {
        $adapter = $this->createMock(MollieHttpAdapterInterface::class);
        $adapter->expects($this->once())->method('send')->with(
            'GET', $expected,
            $this->callback(function ($headers) {
                return $headers['Authorization'] === 'Bearer test_foobarfoobarfoobarfoobarfoobar';
            }), null
        );
        $client = new MollieApiClient($adapter);
        $client->setApiKey('test_foobarfoobarfoobarfoobarfoobar');
        $client->setApiEndpoint($endpoint);
        $client->performHttpCallToFullUrl('GET', $url);
    }

    public static function trustedUrls(): array
    {
        return [
            ['https://api.mollie.com/v2/payments?from=tr_x', 'https://api.mollie.com', 'https://api.mollie.com/v2/payments?from=tr_x'],
            ['https://API.Mollie.com:443/v2/payments', 'https://api.mollie.com', 'https://API.Mollie.com:443/v2/payments'],
            ['https://proxy.example:8443/v2/payments', 'https://proxy.example:8443', 'https://proxy.example:8443/v2/payments'],
            ['https://api.mollie.com/v2/payments', 'https://proxy.example', 'https://api.mollie.com/v2/payments'],
            ['https://u:p@proxy.example/v2/payments', 'https://u:p@proxy.example', 'https://u:p@proxy.example/v2/payments'],
            ['https://api.mollie.com/v2/payments#@attacker.example', 'https://api.mollie.com', 'https://api.mollie.com/v2/payments'],
        ];
    }

    /** @dataProvider linkSinks */
    public function testForgedResourceLinksNeverReachAdapter($class, $relation, $method)
    {
        $adapter = $this->createMock(MollieHttpAdapterInterface::class);
        $adapter->expects($this->never())->method('send');
        $client = new MollieApiClient($adapter);
        $client->setAccessToken('access_test');
        $resource = new $class($client);
        $resource->_links = (object) [$relation => (object) ['href' => 'https://attacker.example/v2/data']];
        $this->expectException(UntrustedUrlException::class);
        $resource->{$method}();
    }

    public static function linkSinks(): array
    {
        return [
            [Profile::class, 'payments', 'payments'],
            [Profile::class, 'methods', 'methods'],
            [Profile::class, 'refunds', 'refunds'],
            [Profile::class, 'chargebacks', 'chargebacks'],
            [Payment::class, 'refunds', 'refunds'],
            [Payment::class, 'captures', 'captures'],
            [Payment::class, 'chargebacks', 'chargebacks'],
            [Subscription::class, 'payments', 'payments'],
            [Subscription::class, 'self', 'cancel'],
            [Mandate::class, 'self', 'revoke'],
        ];
    }

    /** @dataProvider paginationSinks */
    public function testForgedPaginationNeverReachesAdapter($relation, $iterate, $backwards)
    {
        $adapter = $this->createMock(MollieHttpAdapterInterface::class);
        $adapter->expects($this->never())->method('send');
        $client = new MollieApiClient($adapter);
        $client->setAccessToken('access_test');
        $links = (object) [
            'next' => (object) ['href' => 'https://attacker.example/v2/payments'],
            'previous' => (object) ['href' => 'https://attacker.example/v2/payments'],
        ];
        $collection = new PaymentCollection($client, 0, $links);
        $this->expectException(UntrustedUrlException::class);
        if ($iterate) {
            iterator_to_array($collection->getAutoIterator($backwards));
        } else {
            $collection->{$relation}();
        }
    }

    public static function paginationSinks(): array
    {
        return [['next', false, false], ['previous', false, true], ['next', true, false], ['previous', true, true]];
    }

    public function testProxyUserInfoMustMatchAndSecretsAreAbsentFromError()
    {
        $adapter = $this->createMock(MollieHttpAdapterInterface::class);
        $adapter->expects($this->never())->method('send');
        $client = new MollieApiClient($adapter);
        $client->setApiEndpoint('https://user:secret@proxy.example');
        $client->setAccessToken('access_secret');
        try {
            $client->performHttpCallToFullUrl('GET', 'https://proxy.example/v2/payments');
            $this->fail('Expected UntrustedUrlException.');
        } catch (UntrustedUrlException $e) {
            $this->assertStringNotContainsString('secret', $e->getMessage());
        }
    }
}
