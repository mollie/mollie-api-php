<?php

namespace Mollie\Api\Helpers;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class MiddlewareHandlersTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_add_request_middleware_and_execute_it(): void
    {
        $middlewareHandlers = new MiddlewareHandlers;

        $middlewareHandlers->onRequest(function (PendingRequest $pendingRequest) {
            $pendingRequest->headers()->add('Foo', 'Bar');

            return $pendingRequest;
        });

        $result = $middlewareHandlers->executeOnRequest(new PendingRequest);

        $this->assertEquals('Bar', $result->headers()->get('Foo'));
    }

    /**
     * @test
     */
    public function it_can_add_response_middleware_and_execute_it(): void
    {
        $middlewareHandlers = new MiddlewareHandlers;

        $middlewareHandlers->onResponse(function (Response $response) {
            $this->assertTrue($response->successful());

            return $response;
        });

        $result = $middlewareHandlers->executeOnRequest(new PendingRequest);
    }
}
