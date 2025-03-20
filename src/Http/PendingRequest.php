<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\IsResponseAware;
use Mollie\Api\Contracts\PayloadRepository;
use Mollie\Api\Contracts\SupportsTestmode;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\Middleware\ApplyIdempotencyKey;
use Mollie\Api\Http\Middleware\ConvertResponseToException;
use Mollie\Api\Http\Middleware\Hydrate;
use Mollie\Api\Http\Middleware\MiddlewarePriority;
use Mollie\Api\Http\Middleware\ResetIdempotencyKey;
use Mollie\Api\Http\PendingRequest\AddTestmodeIfEnabled;
use Mollie\Api\Http\PendingRequest\AuthenticateRequest;
use Mollie\Api\Http\PendingRequest\MergeRequestProperties;
use Mollie\Api\Http\PendingRequest\RemoveTestmodeFromApiAuthenticatedRequests;
use Mollie\Api\Http\PendingRequest\SetBody;
use Mollie\Api\Http\PendingRequest\SetUserAgent;
use Mollie\Api\Traits\HasMiddleware;
use Mollie\Api\Traits\HasRequestProperties;
use Mollie\Api\Traits\ManagesPsrRequests;
use Mollie\Api\Utils\Url;

class PendingRequest
{
    use HasMiddleware;
    use HasRequestProperties;
    use ManagesPsrRequests;

    protected Connector $connector;

    protected Request $request;

    protected ?PayloadRepository $payload = null;

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

        $this->middleware()->merge($request->middleware(), $connector->middleware());

        $this
            ->tap(new AddTestmodeIfEnabled)
            ->tap(new SetUserAgent)
            ->tap(new MergeRequestProperties)
            ->tap(new SetBody)
            ->tap(new AuthenticateRequest)
            ->tap(new RemoveTestmodeFromApiAuthenticatedRequests);

        $this
            ->middleware()

            /** On request */
            ->onRequest(new ApplyIdempotencyKey, 'idempotency')

            /** On response */
            ->onResponse(new ResetIdempotencyKey, 'idempotency')
            ->onResponse(new Hydrate, 'hydrate', MiddlewarePriority::LOW)
            ->onResponse(new ConvertResponseToException, MiddlewarePriority::HIGH);
    }

    public function setTestmode(bool $testmode): self
    {
        if (! $this->request instanceof SupportsTestmode) {
            return $this;
        }

        if ($this->request instanceof SupportsTestmodeInQuery) {
            $this->query()->add('testmode', $testmode);
        } elseif ($this->request instanceof SupportsTestmodeInPayload) {
            $payload = $this->payload();

            if ($payload === null) {
                return $this;
            }

            $payload->add('testmode', $testmode);
        }

        return $this;
    }

    public function getTestmode(): bool
    {
        if (! $this->request instanceof SupportsTestmode) {
            return false;
        }

        if ($this->request instanceof SupportsTestmodeInQuery) {
            return $this->query()->get('testmode', false);
        }

        $payload = $this->payload();

        return $payload
            ? $payload->get('testmode', false)
            : false;
    }

    public function setPayload(PayloadRepository $bodyRepository): self
    {
        $this->payload = $bodyRepository;

        return $this;
    }

    public function payload(): ?PayloadRepository
    {
        return $this->payload;
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

    /**
     * @return Response|IsResponseAware
     */
    public function executeResponseHandlers(Response $response)
    {
        return $this->middleware()->executeOnResponse($response);
    }

    public function executeFatalHandlers(MollieException $exception): MollieException
    {
        return $this->middleware()->executeOnFatal($exception);
    }

    protected function tap(callable $callable): self
    {
        $callable($this);

        return $this;
    }
}
