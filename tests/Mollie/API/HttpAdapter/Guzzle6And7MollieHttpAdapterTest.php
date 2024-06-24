<?php

namespace Tests\Mollie\API\HttpAdapter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Mollie\Api\Contracts\SupportsDebugging;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\HttpAdapter\Guzzle6And7MollieHttpAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class Guzzle6And7MollieHttpAdapterTest extends TestCase
{
    /** @test */
    public function testDebuggingIsSupported()
    {
        $adapter = Guzzle6And7MollieHttpAdapter::createDefault();
        $this->assertTrue($adapter instanceof SupportsDebugging);
        $this->assertFalse($adapter->debuggingIsActive());

        $adapter->enableDebugging();
        $this->assertTrue($adapter->debuggingIsActive());

        $adapter->disableDebugging();
        $this->assertFalse($adapter->debuggingIsActive());
    }

    /** @test */
    public function whenDebuggingAnApiExceptionIncludesTheRequest()
    {
        $guzzleClient = $this->createMock(Client::class);
        $guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class))
            ->willThrowException(
                new ConnectException(
                    'Mock exception',
                    new Request('POST', 'https://api.mollie.com')
                )
            );

        $adapter = new Guzzle6And7MollieHttpAdapter($guzzleClient);
        $adapter->enableDebugging();

        try {
            $adapter->send(
                'POST',
                'https://api.mollie.com/v2/profiles/pfl_v9hTwCvYqw/methods/bancontact',
                [],
                /** @lang JSON */
                '{ "foo": "bar" }'
            );
        } catch (ApiException $e) {
            $exception = invade($e);
            $this->assertInstanceOf(RequestInterface::class, $exception->request);
        }
    }

    /** @test */
    public function whenNotDebuggingAnApiExceptionIsExcludedFromTheRequest()
    {
        $guzzleClient = $this->createMock(Client::class);
        $guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class))
            ->willThrowException(
                new ConnectException(
                    'Mock exception',
                    new Request('POST', 'https://api.mollie.com')
                )
            );

        $adapter = new Guzzle6And7MollieHttpAdapter($guzzleClient);
        $this->assertFalse($adapter->debuggingIsActive());

        try {
            $adapter->send(
                'POST',
                'https://api.mollie.com/v2/profiles/pfl_v9hTwCvYqw/methods/bancontact',
                [],
                /** @lang JSON */
                '{ "foo": "bar" }'
            );
        } catch (ApiException $e) {
            $exception = invade($e);
            $this->assertNull($exception->request);
        }
    }
}
