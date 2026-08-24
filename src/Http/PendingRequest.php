<?php

declare(strict_types=1);

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\IsResponseAware;
use Mollie\Api\Contracts\PayloadRepository;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use Mollie\Api\Http\Middleware\ApplyIdempotencyKey;
use Mollie\Api\Http\Middleware\ConvertResponseToException;
use Mollie\Api\Http\Middleware\Hydrate;
use Mollie\Api\Http\Middleware\MiddlewarePriority;
use Mollie\Api\Http\PendingRequest\AuthenticateRequest;
use Mollie\Api\Http\PendingRequest\HandleTestmode;
use Mollie\Api\Http\PendingRequest\MergeBody;
use Mollie\Api\Http\PendingRequest\MergeRequestProperties;
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

    protected ?bool $testmode = null;

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
            ->tap(new SetUserAgent)
            ->tap(new MergeRequestProperties)
            ->tap(new MergeBody)
            ->tap(new AuthenticateRequest)
            ->tap(new HandleTestmode);

        $this
            ->middleware()

            /** On request */
            ->onRequest(new ApplyIdempotencyKey, 'idempotency')

            /** On response */
            ->onResponse(new ConvertResponseToException, MiddlewarePriority::HIGH)
            ->onResponse(new Hydrate, 'hydrate', MiddlewarePriority::LOW)

            /** Merge the middleware */
            ->merge($connector->middleware(), $request->middleware());
    }

    /**
     * Determine the effective mode used for this request.
     *
     * API keys select the mode themselves. Other authentication methods use
     * the mode carried by the request flags or its final transport inputs.
     */
    public function getTestmode(): bool
    {
        if ($this->testmode !== null) {
            return $this->testmode;
        }

        $authenticator = $this->connector->getAuthenticator();

        if ($authenticator instanceof ApiKeyAuthenticator) {
            return $this->testmode = $authenticator->isTestToken();
        }

        $query = Url::parseQuery($this->getUri()->getQuery());

        return $this->testmode = $this->connector->getTestmode()
            || $this->request->getTestmode()
            || $this->isTrue($query['testmode'] ?? null)
            || $this->isTrue($this->payload?->get('testmode'));
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

    private function isTrue(mixed $value): bool
    {
        if (! is_scalar($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true;
    }
}
