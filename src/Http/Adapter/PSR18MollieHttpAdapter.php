<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Utils\Factories;
use Psr\Http\Client\ClientExceptionInterface;
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

    /**
     * PSR18MollieHttpAdapter constructor.
     */
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
        return new Factories(
            $this->requestFactory,
            $this->responseFactory,
            $this->streamFactory,
            $this->uriFactory,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        $request = $pendingRequest->createPsrRequest();

        try {
            $response = $this->httpClient->sendRequest($request);

            return $this->createResponse($response, $request, $pendingRequest);
        } catch (NetworkExceptionInterface $e) {
            // throw new FailedConnectionException;
        } catch (RequestExceptionInterface $e) {
            if (! method_exists($e, 'getResponse') || ! $response = $e->getResponse()) {
                // throw new FailedConnectionException
            }

            /** @var ResponseInterface $response */
            return $this->createResponse($response, $request, $pendingRequest, $e);
        }
    }

    /**
     * Create a response.
     */
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

    public function version(): string
    {
        return 'PSR18MollieHttpAdapter';
    }
}
