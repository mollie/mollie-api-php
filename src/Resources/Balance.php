<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Traits\HasMode;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Balance extends BaseResource
{
    use HasMode;


    public string $mode;

    public string $id;

    public string $createdAt;

    public string $currency;

    public string $status;

    public Money $availableAmount;

    public Money $incomingAmount;

    public Money $outgoingAmount;

    public string $transferFrequency;

    public Money $transferThreshold;

    public ?string $transferReference = null;

    /**
     * @var \stdClass
     */
    public $transferDestination;

    /**
     * @var \stdClass
     */
    public $_links;
}
