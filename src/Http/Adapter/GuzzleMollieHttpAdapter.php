<?php

namespace Mollie\Api\Http\Adapter;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\RequestOptions as GuzzleRequestOptions;
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Utils\Factories;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class GuzzleMollieHttpAdapter implements HttpAdapterContract
{
    /**
     * Default response timeout (in seconds).
     */
    public const DEFAULT_TIMEOUT = 10;

    /**
     * Default connect timeout (in seconds).
     */
    public const DEFAULT_CONNECT_TIMEOUT = 2;

    protected ClientInterface $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function factories(): Factories
    {
        $factory = new HttpFactory;

        return new Factories(
            $factory,
            $factory,
            $factory,
            $factory,
        );
    }

    /**
     * Instantiate a default adapter with sane configuration for Guzzle.
     */
    public static function createClient(): self
    {
        $retryMiddlewareFactory = new GuzzleRetryMiddlewareFactory;
        $handlerStack = HandlerStack::create();
        $handlerStack->push($retryMiddlewareFactory->retry());

        $client = new Client([
            GuzzleRequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
            GuzzleRequestOptions::TIMEOUT => self::DEFAULT_TIMEOUT,
            GuzzleRequestOptions::CONNECT_TIMEOUT => self::DEFAULT_CONNECT_TIMEOUT,
            'handler' => $handlerStack,
        ]);

        return new GuzzleMollieHttpAdapter($client);
    }

    /**
     * Send a request to the specified Mollie api url.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        $request = $pendingRequest->createPsrRequest();

        try {
            $response = $this->httpClient->send($request, ['http_errors' => false]);

            return $this->createResponse($response, $request, $pendingRequest);
        } catch (ConnectException $e) {
            // throw new FailedConnectionException
        } catch (RequestException $e) {
            if (! $response = $e->getResponse()) {
                // throw new FailedConnection
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

    /**
     * The version number for the underlying http client, if available.
     * This is used to report the UserAgent to Mollie, for convenient support.
     *
     * @example Guzzle/7.0
     */
    public function version(): string
    {
        return 'Guzzle/' . ClientInterface::MAJOR_VERSION;
    }
}
