<?php

declare(strict_types=1);

namespace Tests\Http;

use Mollie\Api\Exceptions\ValidationException;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Middleware\MiddlewarePriority;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Types\Method as HttpMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ResponseMiddlewarePhasesTest extends TestCase
{
    #[Test]
    public function send_runs_raw_middleware_then_hydration_then_resolved_middleware(): void
    {
        $request = $this->hydratableRequest();
        $client = new MockMollieClient([
            $request::class => MockResponse::ok('method-list'),
        ]);
        $trace = [];

        $client->middleware()->onResponse(function (Response $response) use (&$trace): void {
            $trace[] = 'raw low '.get_debug_type($response);
        }, MiddlewarePriority::LOW);
        $client->middleware()->onResponse(function (Response $response) use (&$trace): void {
            $trace[] = 'raw high '.get_debug_type($response);
        }, MiddlewarePriority::HIGH);
        $client->middleware()->onResponse(function (Response $response) use (&$trace): void {
            $trace[] = 'raw medium '.get_debug_type($response);
        });
        $client->middleware()->onResolved(function ($result) use (&$trace): void {
            $trace[] = 'resolved low '.get_debug_type($result);
        }, MiddlewarePriority::LOW);
        $client->middleware()->onResolved(function ($result) use (&$trace): void {
            $trace[] = 'resolved high '.get_debug_type($result);
        }, MiddlewarePriority::HIGH);
        $client->middleware()->onResolved(function ($result) use (&$trace): void {
            $trace[] = 'resolved medium '.get_debug_type($result);
        });

        $result = $client->send($request);

        $this->assertInstanceOf(MethodCollection::class, $result);
        $this->assertSame([
            'raw high '.Response::class,
            'raw medium '.Response::class,
            'raw low '.Response::class,
            'resolved high '.MethodCollection::class,
            'resolved medium '.MethodCollection::class,
            'resolved low '.MethodCollection::class,
        ], $trace);
    }

    #[Test]
    public function invalid_low_priority_raw_result_is_rejected_before_resolved_middleware(): void
    {
        $request = $this->hydratableRequest();
        $client = new MockMollieClient([
            $request::class => MockResponse::ok('method-list'),
        ]);
        $resolvedCalled = false;

        $client->middleware()->onResponse(
            fn (Response $response) => new MethodCollection($client),
            MiddlewarePriority::LOW
        );
        $client->middleware()->onResolved(function ($result) use (&$resolvedCalled): void {
            $resolvedCalled = true;
        });

        try {
            $client->send($request);
            $this->fail('Expected invalid raw middleware result to throw.');
        } catch (\UnexpectedValueException $exception) {
            $this->assertSame(
                'Response middleware must return Mollie\Api\Http\Response or void; '.MethodCollection::class.' returned.',
                $exception->getMessage()
            );
        }

        $this->assertFalse($resolvedCalled);
    }

    #[Test]
    public function non_hydratable_results_remain_the_identical_response_across_the_boundary(): void
    {
        $request = new class extends Request {
            protected static string $method = HttpMethod::GET;

            public function resolveResourcePath(): string
            {
                return 'dummy';
            }
        };
        $client = new MockMollieClient([
            $request::class => MockResponse::ok(['ok' => true]),
        ]);
        $raw = null;
        $resolved = null;

        $client->middleware()->onResponse(function (Response $response) use (&$raw): void {
            $raw = $response;
        });
        $client->middleware()->onResolved(function ($result) use (&$resolved): void {
            $resolved = $result;
        });

        $result = $client->send($request);

        $this->assertSame($raw, $resolved);
        $this->assertSame($raw, $result);
    }

    #[Test]
    public function empty_hydratable_results_remain_the_identical_response_through_a_void_resolved_hook(): void
    {
        $request = $this->hydratableRequest();
        $client = new MockMollieClient([
            $request::class => MockResponse::noContent(),
        ]);
        $raw = null;
        $resolved = null;

        $client->middleware()->onResponse(function (Response $response) use (&$raw): void {
            $raw = $response;
        });
        $client->middleware()->onResolved(function ($result) use (&$resolved): void {
            $resolved = $result;
        });

        $result = $client->send($request);

        $this->assertSame($raw, $resolved);
        $this->assertSame($raw, $result);
    }

    #[Test]
    public function failure_conversion_stops_before_hydration_and_resolved_middleware(): void
    {
        $request = $this->hydratableRequest();
        $client = new MockMollieClient([
            $request::class => MockResponse::unprocessableEntity(),
        ]);
        $rawCalled = false;
        $resolvedCalled = false;

        $client->middleware()->onResponse(function (Response $response) use (&$rawCalled): void {
            $rawCalled = true;
        }, MiddlewarePriority::HIGH);
        $client->middleware()->onResolved(function ($result) use (&$resolvedCalled): void {
            $resolvedCalled = true;
        });

        try {
            $client->send($request);
            $this->fail('Expected unsuccessful response conversion to throw.');
        } catch (ValidationException) {
            $this->assertFalse($rawCalled);
            $this->assertFalse($resolvedCalled);
        }
    }

    private function hydratableRequest(): ResourceHydratableRequest
    {
        return new class extends ResourceHydratableRequest {
            protected static string $method = HttpMethod::GET;

            protected ?string $hydratableResource = MethodCollection::class;

            public function resolveResourcePath(): string
            {
                return 'methods';
            }
        };
    }
}
