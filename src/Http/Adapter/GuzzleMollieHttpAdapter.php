<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Adapter;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\RequestOptions;
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\NetworkRequestException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Utils\Factories;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
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
     * Create a preconfigured Guzzle adapter.
     */
    public static function createClient(): self
    {
        $handlerStack = HandlerStack::create();

        $client = new Client([
            RequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
            RequestOptions::TIMEOUT => self::DEFAULT_TIMEOUT,
            RequestOptions::CONNECT_TIMEOUT => self::DEFAULT_CONNECT_TIMEOUT,
            RequestOptions::HTTP_ERRORS => false,
            'handler' => $handlerStack,
        ]);

        return new GuzzleMollieHttpAdapter($client);
    }

    /**
     * @throws NetworkRequestException
     * @throws RetryableNetworkRequestException
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        $request = $pendingRequest->createPsrRequest();

        try {
            $response = $this->httpClient->send($request);

            return $this->createResponse($response, $request, $pendingRequest);
        } catch (NetworkExceptionInterface $e) {
            throw new RetryableNetworkRequestException($pendingRequest, $e->getMessage(), $e);
        } catch (TooManyRedirectsException $e) {
            throw new NetworkRequestException($pendingRequest, $e, $e->getMessage());
        } catch (RequestExceptionInterface $e) {
            // Guzzle 8 separates failed transfers from HTTP status errors. Its
            // base RequestException no longer exposes getResponse().
            if (! is_a($e, 'GuzzleHttp\\Exception\\ResponseTransferException') && method_exists($e, 'getResponse')) {
                $response = $e->getResponse();

                if ($response instanceof ResponseInterface && ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300)) {
                    return $this->createResponse($response, $request, $pendingRequest, $e);
                }
            }

            // A malformed request or failed partial response is not a complete
            // HTTP response, nor a network failure eligible for automatic retry.
            throw new NetworkRequestException($pendingRequest, $e, $e->getMessage());
        }
    }

    protected function createResponse(
        ResponseInterface $psrResponse,
        RequestInterface $psrRequest,
        PendingRequest $pendingRequest,
        ?Throwable $exception = null
    ): Response {
        return new Response($psrResponse, $psrRequest, $pendingRequest, $exception);
    }

    public function version(): string
    {
        return 'Guzzle/'.ClientInterface::MAJOR_VERSION;
    }
}
