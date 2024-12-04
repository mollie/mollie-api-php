<?php

namespace Tests\Http\Adapter;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Helpers\Arr;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\HasDefaultFactories;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;

class MockMollieHttpAdapter implements HttpAdapterContract
{
    use HasDefaultFactories;

    /**
     * @var array<string, MockResponse>
     */
    private array $expectedResponses;

    public function __construct(array $expectedResponses = [])
    {
        $this->expectedResponses = $expectedResponses;
    }

    /**
     * {@inheritDoc}
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        $requestClass = get_class($pendingRequest->getRequest());

        if (! Arr::has($this->expectedResponses, $requestClass)) {
            throw new \RuntimeException('The request class '.$requestClass.' is not expected.');
        }

        $mockedResponse = $this->getResponse($requestClass);

        return new Response(
            $mockedResponse->createPsrResponse(),
            $pendingRequest->createPsrRequest(),
            $pendingRequest,
        );
    }

    /**
     * Get the mocked response and remove it from the expected responses.
     *
     * @param string $requestClass
     * @return MockResponse
     */
    private function getResponse(string $requestClass): MockResponse
    {
        $mockedResponse = Arr::get($this->expectedResponses, $requestClass);

        if (!($mockedResponse instanceof SequenceMockResponse)) {
            Arr::forget($this->expectedResponses, $requestClass);
            return $mockedResponse;
        }

        $response = $mockedResponse->pop();

        if ($mockedResponse->isEmpty()) {
            Arr::forget($this->expectedResponses, $requestClass);
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function version(): string
    {
        return 'mock-client/2.0';
    }
}
