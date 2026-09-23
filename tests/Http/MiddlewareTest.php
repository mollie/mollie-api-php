<?php

declare(strict_types=1);

namespace Tests\Http;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Middleware;
use Mollie\Api\Http\Middleware\MiddlewarePriority;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\MethodCollection;
use Nyholm\Psr7\Response as PsrResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    #[Test]
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

    #[Test]
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

    #[Test]
    public function response_middleware_runs_in_priority_order_with_raw_responses(): void
    {
        $middleware = new Middleware;
        $response = $this->createMock(Response::class);
        $calls = [];

        $middleware->onResponse(function (Response $received) use (&$calls, $response): void {
            $this->assertSame($response, $received);
            $calls[] = 'low';
        }, MiddlewarePriority::LOW);
        $middleware->onResponse(function (Response $received) use (&$calls, $response): void {
            $this->assertSame($response, $received);
            $calls[] = 'high';
        }, MiddlewarePriority::HIGH);
        $middleware->onResponse(function (Response $received) use (&$calls, $response): void {
            $this->assertSame($response, $received);
            $calls[] = 'medium';
        });

        $this->assertSame($response, $middleware->executeOnResponse($response));
        $this->assertSame(['high', 'medium', 'low'], $calls);
    }

    #[Test]
    public function response_middleware_can_replace_a_response_while_null_and_void_preserve_identity(): void
    {
        $middleware = new Middleware;
        $initial = $this->createMock(Response::class);
        $replacement = $this->createMock(Response::class);

        $middleware->onResponse(fn (Response $response) => $replacement, MiddlewarePriority::HIGH);
        $middleware->onResponse(fn (Response $response) => null);
        $middleware->onResponse(function (Response $response) use ($replacement): void {
            $this->assertSame($replacement, $response);
        }, MiddlewarePriority::LOW);

        $this->assertSame($replacement, $middleware->executeOnResponse($initial));
    }

    #[Test]
    #[DataProvider('invalidResponseMiddlewareResults')]
    public function response_middleware_rejects_invalid_results_immediately(callable $invalidResult, string $type): void
    {
        $middleware = new Middleware;
        $laterCallbackCalled = false;

        $middleware->onResponse(fn (Response $response) => $invalidResult());
        $middleware->onResponse(function (Response $response) use (&$laterCallbackCalled): void {
            $laterCallbackCalled = true;
        }, MiddlewarePriority::LOW);

        try {
            $middleware->executeOnResponse($this->createMock(Response::class));
            $this->fail('Expected invalid response middleware result to throw.');
        } catch (\UnexpectedValueException $exception) {
            $this->assertSame(
                "Response middleware must return Mollie\\Api\\Http\\Response or void; {$type} returned.",
                $exception->getMessage()
            );
        }

        $this->assertFalse($laterCallbackCalled);
    }

    public static function invalidResponseMiddlewareResults(): iterable
    {
        yield 'resource' => [fn () => new AnyResource(new MockMollieClient), AnyResource::class];
        yield 'collection' => [fn () => new MethodCollection(new MockMollieClient), MethodCollection::class];
        yield 'object' => [fn () => new \stdClass, 'stdClass'];
        yield 'scalar' => [fn () => 42, 'int'];
        yield 'PSR response' => [fn () => new PsrResponse, PsrResponse::class];
    }

    #[Test]
    public function resolved_middleware_transforms_in_priority_order_and_preserves_on_null_or_void(): void
    {
        $middleware = new Middleware;
        $initial = new \stdClass;
        $replacement = new \stdClass;
        $calls = [];

        $middleware->onResolved(function ($result) use (&$calls, $replacement) {
            $calls[] = 'low';

            return $replacement;
        }, MiddlewarePriority::LOW);
        $middleware->onResolved(function ($result) use (&$calls) {
            $calls[] = 'high';

            return null;
        }, MiddlewarePriority::HIGH);
        $middleware->onResolved(function ($result) use (&$calls, $initial): void {
            $this->assertSame($initial, $result);
            $calls[] = 'medium';
        });

        $this->assertSame($replacement, $middleware->executeOnResolved($initial));
        $this->assertSame(['high', 'medium', 'low'], $calls);
    }

    #[Test]
    public function response_and_resolved_handlers_merge_separately_and_names_are_phase_local(): void
    {
        $first = new Middleware;
        $second = new Middleware;
        $calls = [];

        $first->onResponse(function (Response $response) use (&$calls): void {
            $calls[] = 'first raw';
        }, 'shared');
        $first->onResolved(function ($result) use (&$calls): void {
            $calls[] = 'first resolved';
        }, 'shared');
        $second->onResponse(function (Response $response) use (&$calls): void {
            $calls[] = 'second raw';
        });
        $second->onResolved(function ($result) use (&$calls): void {
            $calls[] = 'second resolved';
        });

        $first->merge($second);
        $first->executeOnResponse($this->createMock(Response::class));
        $first->executeOnResolved(new \stdClass);

        $this->assertSame(['first raw', 'second raw', 'first resolved', 'second resolved'], $calls);
    }

    #[Test]
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
