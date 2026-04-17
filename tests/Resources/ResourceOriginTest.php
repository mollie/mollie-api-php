<?php

namespace Tests\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\ResourceOrigin;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the additive `getOrigin()`/`setOrigin()` surface on resources
 * and collections. These round-trip tests guard the PR1 contract:
 *
 * - HTTP `Response` satisfies `ResourceOrigin`.
 * - `setResponse()` also populates origin (HTTP path unchanged).
 * - `setOrigin()` with a non-Response origin does NOT populate
 *   `$this->response`.
 * - `getPendingRequest()` is nullable; HTTP origin returns non-null,
 *   non-Response origin returns null.
 */
class ResourceOriginTest extends TestCase
{
    /** @test */
    public function set_response_populates_origin_on_resource(): void
    {
        $connector = $this->createMock(Connector::class);
        $response = $this->createMock(Response::class);

        $payment = new Payment($connector);
        $payment->setResponse($response);

        $this->assertSame($response, $payment->getResponse());
        $this->assertSame($response, $payment->getOrigin());
    }

    /** @test */
    public function set_origin_with_http_response_mirrors_to_response_slot(): void
    {
        $connector = $this->createMock(Connector::class);
        $response = $this->createMock(Response::class);

        $payment = new Payment($connector);
        $payment->setOrigin($response);

        $this->assertSame($response, $payment->getOrigin());
        $this->assertSame($response, $payment->getResponse());
    }

    /** @test */
    public function set_origin_with_non_response_origin_does_not_populate_response_slot(): void
    {
        $connector = $this->createMock(Connector::class);
        $origin = new class($connector) implements ResourceOrigin {
            private Connector $connector;

            public function __construct(Connector $connector)
            {
                $this->connector = $connector;
            }

            public function getConnector(): Connector
            {
                return $this->connector;
            }
        };

        $payment = new Payment($connector);
        $payment->setOrigin($origin);

        $this->assertSame($origin, $payment->getOrigin());
    }

    /** @test */
    public function get_pending_request_returns_non_null_for_http_origin(): void
    {
        $connector = $this->createMock(Connector::class);
        $pendingRequest = $this->createMock(PendingRequest::class);
        $response = $this->createMock(Response::class);
        $response->method('getPendingRequest')->willReturn($pendingRequest);

        $payment = new Payment($connector);
        $payment->setResponse($response);

        $this->assertSame($pendingRequest, $payment->getPendingRequest());
    }

    /** @test */
    public function get_pending_request_returns_null_when_origin_is_not_http(): void
    {
        $connector = $this->createMock(Connector::class);
        $origin = new class($connector) implements ResourceOrigin {
            private Connector $connector;

            public function __construct(Connector $connector)
            {
                $this->connector = $connector;
            }

            public function getConnector(): Connector
            {
                return $this->connector;
            }
        };

        $payment = new Payment($connector);
        $payment->setOrigin($origin);

        $this->assertNull($payment->getPendingRequest());
    }

    /** @test */
    public function get_origin_returns_null_when_never_set(): void
    {
        $connector = $this->createMock(Connector::class);

        $payment = new Payment($connector);

        $this->assertNull($payment->getOrigin());
    }
}
