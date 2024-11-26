<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\SupportsResourceHydration;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Traits\HandlesResourceCreation;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use stdClass;
use Throwable;

class Response
{
    use HandlesResourceCreation;

    protected ResponseInterface $psrResponse;

    protected RequestInterface $psrRequest;

    protected PendingRequest $pendingRequest;

    protected ?Throwable $senderException = null;

    /**
     * The decoded JSON response.
     */
    protected ?\stdClass $decoded = null;

    public function __construct(
        ResponseInterface $psrResponse,
        RequestInterface $psrRequest,
        PendingRequest $pendingRequest,
        ?Throwable $senderException = null
    ) {
        $this->psrResponse = $psrResponse;
        $this->psrRequest = $psrRequest;
        $this->pendingRequest = $pendingRequest;
        $this->senderException = $senderException;
    }

    /**
     * @return mixed
     */
    public function toResource()
    {
        if (! $this->getRequest() instanceof ResourceHydratableRequest) {
            return $this;
        }

        return $this->createResource($this->getRequest(), $this);
    }

    /**
     * Get the JSON decoded body of the response as an array or scalar value.
     */
    public function json(): stdClass
    {
        if (! $this->decoded) {
            $this->decoded = @json_decode($body = $this->body() ?: '[]');

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException("Unable to decode Mollie response: '{$body}'.");
            }
        }

        return $this->decoded;
    }

    public function getConnector(): Connector
    {
        return $this->pendingRequest->getConnector();
    }

    public function getPendingRequest(): PendingRequest
    {
        return $this->pendingRequest;
    }

    public function getRequest(): Request
    {
        return $this->pendingRequest->getRequest();
    }

    public function getPsrRequest(): RequestInterface
    {
        return $this->psrRequest;
    }

    public function getPsrResponse(): ResponseInterface
    {
        return $this->psrResponse;
    }

    public function getSenderException(): ?Throwable
    {
        return $this->senderException;
    }

    /**
     * Get the body of the response as string.
     */
    public function body(): string
    {
        $stream = $this->stream();

        $contents = $stream->getContents();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        return $contents;
    }

    /**
     * Get the body as a stream.
     */
    public function stream(): StreamInterface
    {
        $stream = $this->psrResponse->getBody();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        return $stream;
    }

    public function status(): int
    {
        return $this->psrResponse->getStatusCode();
    }

    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function isUnprocessable(): bool
    {
        return $this->status() === ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY;
    }

    public function isEmpty(): bool
    {
        return empty($this->body());
    }
}
