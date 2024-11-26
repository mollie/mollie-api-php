<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\HasResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;

#[\AllowDynamicProperties]
abstract class BaseResource implements HasResponse
{
    /** @var MollieApiClient */
    protected Connector $connector;

    protected ?Response $response;

    /**
     * Indicates the type of resource.
     *
     * @example payment
     *
     * @var string
     */
    public $resource;

    public function __construct(Connector $connector, ?Response $response = null)
    {
        $this->connector = $connector;
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function getPendingRequest(): ?PendingRequest
    {
        return $this->response
            ? $this->response->getPendingRequest()
            : null;
    }
}
