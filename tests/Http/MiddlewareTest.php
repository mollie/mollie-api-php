<?php

namespace Tests\Http;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Middleware;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_add_request_middleware_and_execute_it(): void
    {
        $middlewareHandlers = new Middleware;

        $middlewareHandlers->onRequest(function (PendingRequest $pendingRequest) {
            $pendingRequest->headers()->add('Foo', 'Bar');

            return $pendingRequest;
        });

        $result = $middlewareHandlers->executeOnRequest(
            new PendingRequest(new MockMollieClient, new DynamicGetRequest(''))
        );

        $this->assertEquals('Bar', $result->headers()->get('Foo'));
    }

    /**
     * @test
     */
    public function it_can_add_response_middleware_and_execute_it(): void
    {
        $middlewareHandlers = new Middleware;

        $middlewareHandlers->onResponse(function (Response $response) {
            $this->assertTrue($response->successful());

            return $response;
        });

        // Create a mock response
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(true);

        $result = $middlewareHandlers->executeOnResponse($responseMock);

        $this->assertTrue($result->successful());
    }

    /**
     * @test
     */
    public function it_can_merge_middleware_handlers(): void
    {
        $middlewareHandlers1 = new Middleware;
        $middlewareHandlers2 = new Middleware;

        $middlewareHandlers1->onRequest(function (PendingRequest $pendingRequest) {
            $pendingRequest->headers()->add('Request-One', 'One');

            return $pendingRequest;
        });

        $middlewareHandlers2->onRequest(function (PendingRequest $pendingRequest) {
            $pendingRequest->headers()->add('Request-Two', 'Two');

            return $pendingRequest;
        });

        $middlewareHandlers1->merge($middlewareHandlers2);

        $result = $middlewareHandlers1->executeOnRequest(
            new PendingRequest(new MockMollieClient, new DynamicGetRequest(''))
        );

        $this->assertEquals('One', $result->headers()->get('Request-One'));
        $this->assertEquals('Two', $result->headers()->get('Request-Two'));
    }
}
