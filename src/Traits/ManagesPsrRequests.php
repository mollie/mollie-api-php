<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\PayloadRepository;
use Mollie\Api\Helpers\Factories;
use Mollie\Api\Helpers\Url;
use Mollie\Api\Http\PendingRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @mixin PendingRequest
 */
trait ManagesPsrRequests
{
    protected Factories $factoryCollection;

    public function createPsrRequest(): RequestInterface
    {
        $factories = $this->factoryCollection;

        $request = $factories->requestFactory->createRequest(
            $this->method(),
            $this->getUri(),
        );

        foreach ($this->headers()->all() as $headerName => $headerValue) {
            $request = $request->withHeader($headerName, $headerValue);
        }

        if ($this->payload() instanceof PayloadRepository) {
            $request = $request->withBody($this->payload()->toStream($factories->streamFactory));
        }

        return $request;
    }

    public function getUri(): UriInterface
    {
        $uri = $this
            ->factoryCollection
            ->uriFactory
            ->createUri($this->url());

        $existingQuery = Url::parseQuery($uri->getQuery());

        return $uri->withQuery(
            http_build_query(array_merge($existingQuery, $this->query()->all()))
        );
    }

    /**
     * Get the factory collection
     */
    public function getFactoryCollection(): Factories
    {
        return $this->factoryCollection;
    }
}