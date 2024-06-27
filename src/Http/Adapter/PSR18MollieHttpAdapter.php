<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Contracts\MollieHttpAdapterContract;
use Mollie\Api\Contracts\ResponseContract;
use Mollie\Api\Contracts\SupportsDebuggingContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\IsDebuggable;
use Mollie\Api\Http\PsrResponseHandler;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class PSR18MollieHttpAdapter implements MollieHttpAdapterContract, SupportsDebuggingContract
{
    use IsDebuggable;

    /**
     * @var ClientInterface
     */
    private ClientInterface $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private RequestFactoryInterface $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private StreamFactoryInterface $streamFactory;

    /**
     * PSR18MollieHttpAdapter constructor.
     *
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $method, string $url, $headers, ?string $body = null): ResponseContract
    {
        try {
            $request = $this->createRequest($method, $url, $headers, $body ?? '');
            $response = $this->httpClient->sendRequest($request);

            return PsrResponseHandler::create()
                ->handle($request, $response, $response->getStatusCode(), $body);
        } catch (\Exception $e) {
            if (! $this->debug) {
                $request = null;
            }

            throw new ApiException(
                "Error while sending request to Mollie API: " . $e->getMessage(),
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

    /**
     * Create a PSR-7 request.
     *
     * @param string $httpMethod
     * @param string $url
     * @param string|array $headers
     * @param string $httpBody
     * @return RequestInterface
     */
    private function createRequest(string $httpMethod, string $url, $headers, ?string $httpBody): RequestInterface
    {
        $stream = $this->streamFactory->createStream($httpBody);

        $request = $this
            ->requestFactory
            ->createRequest($httpMethod, $url)
            ->withBody($stream);

        return $this->addHeadersToRequest($request, $headers);
    }

    /**
     * Parse and add headers to request.
     *
     * @param RequestInterface $request
     * @param string|array $headers
     * @return RequestInterface
     */
    private function addHeadersToRequest(RequestInterface $request, $headers): RequestInterface
    {
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                $request = $request->withHeader($name, $value);
            }
        }

        if (is_string($headers)) {
            $headerLines = explode("\r\n", $headers);

            foreach ($headerLines as $line) {
                list($name, $value) = explode(': ', $line, 2);
                $request = $request->withHeader($name, $value);
            }
        }

        return $request;
    }
}
