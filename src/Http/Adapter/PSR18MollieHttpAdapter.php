<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Contracts\SupportsDebuggingContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\IsDebuggableAdapter;
use Mollie\Api\Utils\Factories;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class PSR18MollieHttpAdapter implements HttpAdapterContract, SupportsDebuggingContract
{
    use IsDebuggableAdapter;

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

            return new Response(
                $response,
                $request,
                $pendingRequest
            );
        } catch (ClientExceptionInterface $e) {
            if (! $this->debug) {
                $request = null;
            }

            throw new ApiException(
                'Error while sending request to Mollie API: '.$e->getMessage(),
                0,
                $e,
                $request,
                null
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function version(): string
    {
        return 'PSR18MollieHttpAdapter';
    }
}
