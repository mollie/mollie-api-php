<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\BodyRepository;
use Mollie\Api\Contracts\Connector;
use Mollie\Api\Helpers\Url;
use Mollie\Api\Http\Middleware\ApplyIdempotencyKey;
use Mollie\Api\Http\Middleware\GuardResponse;
use Mollie\Api\Http\Middleware\ResetIdempotencyKey;
use Mollie\Api\Http\Middleware\ThrowExceptionIfRequestFailed;
use Mollie\Api\Http\PendingRequest\AuthenticateRequest;
use Mollie\Api\Http\PendingRequest\MergeRequestProperties;
use Mollie\Api\Http\PendingRequest\SetBody;
use Mollie\Api\Http\PendingRequest\SetUserAgent;
use Mollie\Api\Http\PendingRequest\ValidateProperties;
use Mollie\Api\Traits\HasMiddleware;
use Mollie\Api\Traits\HasRequestProperties;
use Mollie\Api\Traits\ManagesPsrRequests;

class PendingRequest
{
    use HasMiddleware;
    use HasRequestProperties;
    use ManagesPsrRequests;

    protected Connector $connector;

    protected Request $request;

    protected ?BodyRepository $body = null;

    protected string $method;

    /**
     * The URL the request will be made to.
     */
    protected string $url;

    public function __construct(Connector $connector, Request $request)
    {
        $this->factoryCollection = $connector->getHttpClient()->factories();

        $this->connector = $connector;
        $this->request = $request;

        $this->method = $request->getMethod();
        $this->url = Url::join($connector->resolveBaseUrl(), $request->resolveResourcePath());

        $this
            ->tap(new MergeRequestProperties)
            ->tap(new ValidateProperties)
            ->tap(new SetBody)
            ->tap(new SetUserAgent)
            ->tap(new AuthenticateRequest);

        $this
            ->middleware()
            ->onRequest(new ApplyIdempotencyKey)
            ->onResponse(new ResetIdempotencyKey)
            ->onResponse(new GuardResponse)
            ->onResponse(new ThrowExceptionIfRequestFailed);
    }

    public function setPayload(BodyRepository $bodyRepository): static
    {
        $this->body = $bodyRepository;

        return $this;
    }

    public function body(): ?BodyRepository
    {
        return $this->body;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function executeRequestHandlers(): self
    {
        return $this->middleware()->executeOnRequest($this);
    }

    public function executeResponseHandlers(Response $response): Response
    {
        return $this->middleware()->executeOnResponse($response);
    }

    protected function tap(callable $callable): static
    {
        $callable($this);

        return $this;
    }
}
