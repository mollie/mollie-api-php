<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\IsResponseAware;
use Mollie\Api\Traits\HasResponse;

#[\AllowDynamicProperties]
abstract class BaseResource implements IsResponseAware
{
    use HasResponse;

    protected Connector $connector;

    /**
     * Indicates the type of resource.
     *
     * @var string
     */
    public $resource;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }
}
