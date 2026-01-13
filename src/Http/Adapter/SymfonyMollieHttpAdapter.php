<?php

namespace Mollie\Api\Http\Adapter;

use Composer\InstalledVersions;
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\HasDefaultFactories;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Component\HttpClient\Response\StreamableInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SymfonyMollieHttpAdapter implements HttpAdapterContract
{
    use HasDefaultFactories;

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        $options = [
            'headers' => $pendingRequest->headers()->all(),
            'query' => $pendingRequest->query()->all(),
        ];
        if ($payload = $pendingRequest->payload()) {
            $options['json'] = $payload->all();
        }

        $response = $this->httpClient->request(
            $pendingRequest->method(),
            $pendingRequest->url(),
            $options,
        );

        try {
            $psrResponse = $this->getPsrResponse($response);
        } catch (TransportExceptionInterface $e) {
            throw new RetryableNetworkRequestException($pendingRequest, $e->getMessage(), $e);
        }

        return new Response(
            $psrResponse,
            $pendingRequest->createPsrRequest(),
            $pendingRequest,
        );
    }

    public function version(): string
    {
        if (class_exists(InstalledVersions::class)) {
            return 'SymfonyHttpClient/' . InstalledVersions::getVersion('symfony/http-client');
        }

        return 'SymfonyHttpClient';
    }

    /** @throws TransportExceptionInterface */
    public function getPsrResponse(ResponseInterface $response): PsrResponseInterface
    {
        $psrResponse = $this->factories()->responseFactory
            ->createResponse($response->getStatusCode())
        ;

        foreach ($response->getHeaders(false) as $name => $headers) {
            foreach ($headers as $header) {
                $psrResponse = $psrResponse->withAddedHeader($name, $header);
            }
        }

        return $psrResponse->withBody(
            $response instanceof StreamableInterface
                ? $this->factories()->streamFactory->createStreamFromResource($response->toStream(false))
                : $this->factories()->streamFactory->createStream($response->getContent(false)),
        );
    }
}
