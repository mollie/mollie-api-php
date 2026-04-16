<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Route extends BaseResource
{
    public string $id;

    public string $paymentId;

    public Money $amount;

    /**
     * @var \stdClass
     */
    public $destination;

    public string $releaseDate;
}
