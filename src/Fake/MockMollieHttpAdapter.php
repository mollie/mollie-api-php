<?php

namespace Mollie\Api\Fake;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\HasDefaultFactories;
use Mollie\Api\Utils\Arr;
use PHPUnit\Framework\Assert as PHPUnit;

class MockMollieHttpAdapter implements HttpAdapterContract
{
    use HasDefaultFactories;

    /**
     * @var array<string, MockResponse>
     */
    private array $expected;

    private array $recorded = [];

    public function __construct(array $expectedResponses = [])
    {
        $this->expected = $expectedResponses;
    }

    /**
     * {@inheritDoc}
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        $requestClass = get_class($request = $pendingRequest->getRequest());

        $this->guardAgainstStrayRequests($requestClass);

        $mockedResponse = $this->getResponse($requestClass);

        $response = new Response(
            $mockedResponse->createPsrResponse(),
            $pendingRequest->createPsrRequest(),
            $pendingRequest,
        );

        $this->recorded[] = [$request, $response];

        return $response;
    }

    private function guardAgainstStrayRequests(string $requestClass): void
    {
        if (! Arr::has($this->expected, $requestClass)) {
            throw new \RuntimeException('The request class '.$requestClass.' is not expected.');
        }
    }

    /**
     * Get the mocked response and remove it from the expected responses.
     */
    private function getResponse(string $requestClass): MockResponse
    {
        $mockedResponse = Arr::get($this->expected, $requestClass);

        if (! ($mockedResponse instanceof SequenceMockResponse)) {
            Arr::forget($this->expected, $requestClass);

            return $mockedResponse;
        }

        $response = $mockedResponse->pop();

        if ($mockedResponse->isEmpty()) {
            Arr::forget($this->expected, $requestClass);
        }

        return $response;
    }

    public function recorded(?callable $callback = null): array
    {
        if ($callback === null) {
            return $this->recorded;
        }

        return array_filter($this->recorded, fn ($recorded) => $callback($recorded[0], $recorded[1]));
    }

    /**
     * @param  string|callable  $callback
     */
    public function assertSent($callback): void
    {
        if (is_string($callback)) {
            $callback = fn ($request) => get_class($request) === $callback;
        }

        PHPUnit::assertTrue(
            count($this->recorded($callback)) > 0,
            'No requests were sent.'
        );
    }

    public function assertSentCount(int $count): void
    {
        PHPUnit::assertEquals(
            $count,
            count($this->recorded),
            'The expected number of requests was not sent.'
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
