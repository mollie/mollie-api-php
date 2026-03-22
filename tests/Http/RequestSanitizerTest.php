<?php

namespace Tests\Http;

use GuzzleHttp\Psr7\Request;
use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\RequestSanitizer;
use Mollie\Api\Http\Response;
use Mollie\Api\Repositories\JsonPayloadRepository;
use Mollie\Api\Traits\HasDefaultFactories;
use PHPUnit\Framework\TestCase;

class RequestSanitizerTest extends TestCase
{
    use HasDefaultFactories;

    /** @test */
    public function it_removes_sensitive_headers_from_request(): void
    {
        $factories = $this->factories();

        // Create a PSR request with sensitive and non-sensitive headers
        $psrRequest = (new Request('GET', 'https://api.mollie.com/v2/payments'))
            ->withHeader('Authorization', 'Bearer test_token')
            ->withHeader('User-Agent', 'Mollie/1.0')
            ->withHeader('X-Mollie-Client-Info', 'PHP/8.0')
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');

        // Create a PSR response
        $psrResponse = $factories->responseFactory->createResponse(500)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($factories->streamFactory->createStream('{"error": "Internal Server Error"}'));

        // Create a PendingRequest with a payload
        $client = new MockMollieClient();
        $pendingRequest = new PendingRequest($client, new DynamicGetRequest(''));
        $payload = new JsonPayloadRepository(['sensitive' => 'data', 'key' => 'value']);
        $pendingRequest->setPayload($payload);

        // Create a Response
        $response = new Response($psrResponse, $psrRequest, $pendingRequest);

        // Create a RequestException
        $exception = new RequestException($response, 'Request failed');

        // Sanitize the exception
        $sanitizer = new RequestSanitizer();
        $sanitizedException = $sanitizer->sanitize($exception);

        // Assert sensitive headers are removed
        $sanitizedRequest = $sanitizedException->getRequest();
        $this->assertFalse($sanitizedRequest->hasHeader('Authorization'));
        $this->assertFalse($sanitizedRequest->hasHeader('User-Agent'));
        $this->assertFalse($sanitizedRequest->hasHeader('X-Mollie-Client-Info'));

        // Assert non-sensitive headers remain
        $this->assertTrue($sanitizedRequest->hasHeader('Accept'));
        $this->assertTrue($sanitizedRequest->hasHeader('Content-Type'));
        $this->assertEquals(['application/json'], $sanitizedRequest->getHeader('Accept'));
    }

    /** @test */
    public function it_clears_payload_from_pending_request(): void
    {
        $factories = $this->factories();

        $psrRequest = new Request('GET', 'https://api.mollie.com/v2/payments');
        $psrResponse = $factories->responseFactory->createResponse(500)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($factories->streamFactory->createStream('{"error": "Internal Server Error"}'));

        $client = new MockMollieClient();
        $pendingRequest = new PendingRequest($client, new DynamicGetRequest(''));
        $payload = new JsonPayloadRepository(['sensitive' => 'data', 'key' => 'value']);
        $pendingRequest->setPayload($payload);

        $response = new Response($psrResponse, $psrRequest, $pendingRequest);
        $exception = new RequestException($response, 'Request failed');

        $sanitizer = new RequestSanitizer();
        $sanitizedException = $sanitizer->sanitize($exception);

        // Assert payload is cleared (should be a new empty JsonPayloadRepository)
        $sanitizedPendingRequest = $sanitizedException->getPendingRequest();
        $sanitizedPayload = $sanitizedPendingRequest->payload();

        $this->assertNotNull($sanitizedPayload);
        $this->assertInstanceOf(JsonPayloadRepository::class, $sanitizedPayload);
        $this->assertTrue($sanitizedPayload->isEmpty());
    }

    /** @test */
    public function it_returns_the_same_exception_instance(): void
    {
        $factories = $this->factories();

        $psrRequest = new Request('GET', 'https://api.mollie.com/v2/payments');
        $psrResponse = $factories->responseFactory->createResponse(500)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($factories->streamFactory->createStream('{"error": "Internal Server Error"}'));

        $client = new MockMollieClient();
        $pendingRequest = new PendingRequest($client, new DynamicGetRequest(''));
        $response = new Response($psrResponse, $psrRequest, $pendingRequest);
        $exception = new RequestException($response, 'Request failed');

        $sanitizer = new RequestSanitizer();
        $sanitizedException = $sanitizer->sanitize($exception);

        $this->assertSame($exception, $sanitizedException);
    }

    /** @test */
    public function it_handles_case_insensitive_header_names(): void
    {
        $factories = $this->factories();

        // Test with lowercase header name (authorization vs Authorization)
        $psrRequest = (new Request('GET', 'https://api.mollie.com/v2/payments'))
            ->withHeader('authorization', 'Bearer test_token')
            ->withHeader('user-agent', 'Mollie/1.0')
            ->withHeader('x-mollie-client-info', 'PHP/8.0');

        $psrResponse = $factories->responseFactory->createResponse(500)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($factories->streamFactory->createStream('{"error": "Internal Server Error"}'));

        $client = new MockMollieClient();
        $pendingRequest = new PendingRequest($client, new DynamicGetRequest(''));
        $response = new Response($psrResponse, $psrRequest, $pendingRequest);
        $exception = new RequestException($response, 'Request failed');

        $sanitizer = new RequestSanitizer();
        $sanitizedException = $sanitizer->sanitize($exception);

        // Assert lowercase sensitive headers are also removed
        $sanitizedRequest = $sanitizedException->getRequest();
        $this->assertFalse($sanitizedRequest->hasHeader('authorization'));
        $this->assertFalse($sanitizedRequest->hasHeader('user-agent'));
        $this->assertFalse($sanitizedRequest->hasHeader('x-mollie-client-info'));
    }

    /** @test */
    public function it_handles_request_without_sensitive_headers(): void
    {
        $factories = $this->factories();

        $psrRequest = (new Request('GET', 'https://api.mollie.com/v2/payments'))
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');

        $psrResponse = $factories->responseFactory->createResponse(500)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($factories->streamFactory->createStream('{"error": "Internal Server Error"}'));

        $client = new MockMollieClient();
        $pendingRequest = new PendingRequest($client, new DynamicGetRequest(''));
        $response = new Response($psrResponse, $psrRequest, $pendingRequest);
        $exception = new RequestException($response, 'Request failed');

        $sanitizer = new RequestSanitizer();
        $sanitizedException = $sanitizer->sanitize($exception);

        // Should not throw an error and headers should remain
        $sanitizedRequest = $sanitizedException->getRequest();
        $this->assertTrue($sanitizedRequest->hasHeader('Accept'));
        $this->assertTrue($sanitizedRequest->hasHeader('Content-Type'));
    }
}
