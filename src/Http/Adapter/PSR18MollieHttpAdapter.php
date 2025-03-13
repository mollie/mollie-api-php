<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\NetworkRequestException;
use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Utils\Factories;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Throwable;

final class PSR18MollieHttpAdapter implements HttpAdapterContract
{
    private ClientInterface $httpClient;

    private RequestFactoryInterface $requestFactory;

    private ResponseFactoryInterface $responseFactory;

    private StreamFactoryInterface $streamFactory;

    private UriFactoryInterface $uriFactory;

    private ?Factories $factories = null;

    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        UriFactoryInterface $uriFactory
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->uriFactory = $uriFactory;
    }

    public function factories(): Factories
    {
        return $this->factories ??= new Factories(
            $this->requestFactory,
            $this->responseFactory,
            $this->streamFactory,
            $this->uriFactory,
        );
    }

    /**
     * Send a request using a PSR-18 compatible HTTP client.
     *
     * @throws NetworkRequestException When a network error occurs
     * @throws RetryableNetworkRequestException When a temporary network error occurs
     * @throws RequestException When the request fails with a response
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        $request = $pendingRequest->createPsrRequest();

        try {
            $response = $this->httpClient->sendRequest($request);

            return $this->createResponse($response, $request, $pendingRequest);
        } catch (NetworkExceptionInterface $e) {
            // PSR-18 NetworkExceptionInterface indicates network errors, which are retryable
            throw new RetryableNetworkRequestException(
                $pendingRequest,
                'Network error: '.$e->getMessage()
            );
        } catch (RequestExceptionInterface $e) {
            if (method_exists($e, 'getResponse') && $response = $e->getResponse()) {
                return $this->createResponse($response, $request, $pendingRequest, $e);
            }

            throw new RetryableNetworkRequestException(
                $pendingRequest,
                'Network error: '.$e->getMessage()
            );
        }
    }

    protected function createResponse(
        ResponseInterface $psrResponse,
        RequestInterface $psrRequest,
        PendingRequest $pendingRequest,
        ?Throwable $exception = null
    ): Response {
        return new Response(
            $psrResponse,
            $psrRequest,
            $pendingRequest,
            $exception
        );
    }

    /**
     * Get the version string for the HTTP client implementation.
     * This is used in the User-Agent header.
     */
    public function version(): string
    {
        $clientClass = get_class($this->httpClient);
        $clientName = substr($clientClass, strrpos($clientClass, '\\') + 1);

        return 'PSR18/'.$clientName;
    }
}
