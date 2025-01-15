<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Exceptions\JsonParseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use stdClass;
use Throwable;

class Response
{
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
     * Get the JSON decoded body of the response as an array or scalar value.
     */
    public function json(): stdClass
    {
        if (! $this->decoded) {
            $this->decoded = $this->decodeJson();
        }

        return $this->decoded;
    }

    private function decodeJson(): stdClass
    {
        $decoded = json_decode($body = $this->body() ?: '{}');

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonParseException("Invalid JSON response from Mollie: '{$body}'.", $body);
        }

        return $decoded;
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
