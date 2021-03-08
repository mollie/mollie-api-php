<?php

namespace Tests\Mollie\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\HttpAdapter\Guzzle6And7RetryMiddlewareFactory;
use PHPUnit\Framework\TestCase;

class RetryMiddlewareFactoryTest extends TestCase
{
    public function testRetriesConnectException()
    {
        $middlewareFactory = new Guzzle6And7RetryMiddlewareFactory;

        $mock = new MockHandler(
            [
                new ConnectException("Error 1", new Request('GET', 'test')),
                new Response(200, ['X-Foo' => 'Bar']),
            ]
        );

        $handler = HandlerStack::create($mock);
        $handler->push($middlewareFactory->retry(false));
        $client = new Client(['handler' => $handler]);

        $finalResponse = $client->request('GET', '/');

        $this->assertEquals(200, $finalResponse->getStatusCode());
    }

    public function testRetryLimit()
    {
        $middlewareFactory = new Guzzle6And7RetryMiddlewareFactory;

        $mock = new MockHandler(
            [
                new ConnectException("Error 1", new Request('GET', 'test')),
                new ConnectException("Error 2", new Request('GET', 'test')),
                new ConnectException("Error 3", new Request('GET', 'test')),
                new ConnectException("Error 4", new Request('GET', 'test')),
                new ConnectException("Error 5", new Request('GET', 'test')),
                new ConnectException("Error 6", new Request('GET', 'test')),
            ]
        );

        $handler = HandlerStack::create($mock);
        $handler->push($middlewareFactory->retry(false));
        $client = new Client([ 'handler' => $handler ]);

        $this->expectException(ConnectException::class);
        $this->expectExceptionMessage("Error 6");

        $client->request('GET', '/')->getStatusCode();
    }

    public function testRetryDelay()
    {
        $middlewareFactory = new Guzzle6And7RetryMiddlewareFactory;

        $mock = new MockHandler(
            [
                new ConnectException("+1 second delay", new Request('GET', 'test')),
                new ConnectException("+2 second delay", new Request('GET', 'test')),
                new Response(200),
            ]
        );

        $handler = HandlerStack::create($mock);
        $handler->push($middlewareFactory->retry(true));
        $client = new Client([ 'handler' => $handler ]);

        $startTime = time();
        $client->request('GET', '/')->getStatusCode();
        $endTime = time();

        $this->assertGreaterThan($startTime + 2, $endTime);
    }
}
