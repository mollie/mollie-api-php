<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Issuer extends BaseResource
{
    public string $id;

    public string $name;

    public string $method;

    /**
     * @var \stdClass
     */
    public $image;
}
