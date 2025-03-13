<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\IsResponseAware;
use Mollie\Api\Contracts\ViableResponse;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\Middleware\Handlers;
use Mollie\Api\Http\Middleware\MiddlewarePriority;

class Middleware
{
    protected Handlers $onRequest;

    protected Handlers $onResponse;

    protected Handlers $onFatal;

    public function __construct()
    {
        $this->onRequest = new Handlers;
        $this->onResponse = new Handlers;
        $this->onFatal = new Handlers;
    }

    public function onRequest(callable $callback, ?string $name = null, string $priority = MiddlewarePriority::MEDIUM): self
    {
        $this->onRequest->add(static function (PendingRequest $pendingRequest) use ($callback): PendingRequest {
            $result = $callback($pendingRequest);

            if ($result instanceof PendingRequest) {
                return $result;
            }

            return $pendingRequest;
        }, $name, $priority);

        return $this;
    }

    public function onResponse(callable $callback, ?string $name = null, string $priority = MiddlewarePriority::MEDIUM): self
    {
        /** @param Response|IsResponseAware $response */
        $this->onResponse->add(static function ($response) use ($callback) {
            $result = $callback($response);

            return $result instanceof Response
                || $result instanceof ViableResponse
                ? $result
                : $response;
        }, $name, $priority);

        return $this;
    }

    public function onFatal(callable $callback, ?string $name = null, string $priority = MiddlewarePriority::MEDIUM): self
    {
        $this->onFatal->add(static function (MollieException $exception) use ($callback) {
            $callback($exception);

            return $exception;
        }, $name, $priority);

        return $this;
    }

    public function executeOnRequest(PendingRequest $pendingRequest): PendingRequest
    {
        return $this->onRequest->execute($pendingRequest);
    }

    /**
     * @return Response|ViableResponse
     */
    public function executeOnResponse(Response $response)
    {
        return $this->onResponse->execute($response);
    }

    public function executeOnFatal(MollieException $exception): MollieException
    {
        return $this->onFatal->execute($exception);
    }

    /**
     * @param  array<Middleware>  ...$handlersCollection
     */
    public function merge(...$handlersCollection): self
    {
        /** @var Middleware $handlers */
        foreach ($handlersCollection as $handlers) {
            $onRequestHandlers = array_merge(
                $this->onRequest->getHandlers(),
                $handlers->onRequest->getHandlers()
            );

            $this->onRequest->setHandlers($onRequestHandlers);

            $onResponseHandlers = array_merge(
                $this->onResponse->getHandlers(),
                $handlers->onResponse->getHandlers()
            );

            $this->onResponse->setHandlers($onResponseHandlers);

            $onFatalHandlers = array_merge(
                $this->onFatal->getHandlers(),
                $handlers->onFatal->getHandlers()
            );
            $this->onFatal->setHandlers($onFatalHandlers);
        }

        return $this;
    }
}
