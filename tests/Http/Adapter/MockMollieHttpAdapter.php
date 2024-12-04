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
        if (! Arr::has($this->expectedResponses, $requestClass = get_class($pendingRequest->getRequest()))) {
            throw new \RuntimeException('The request class '.$requestClass.' is not expected.');
        }

        $mockedResponse = $this->expectedResponses[$requestClass];

        if ($mockedResponse instanceof SequenceMockResponse) {
            $mockedResponse = $mockedResponse->pop();
        }

        return new Response(
            $mockedResponse->createPsrResponse(),
            $pendingRequest->createPsrRequest(),
            $pendingRequest,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function version(): string
    {
        return 'mock-client/2.0';
    }
}
