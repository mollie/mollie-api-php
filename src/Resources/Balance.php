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

    /**
     * Required by the API. Defaults to null so payloads recorded before this field
     * was modelled (fixtures, consumer mocks) keep hydrating.
     */
    public ?Money $pendingAmount = null;

    /**
     * @deprecated Not part of the Balance API response; kept for callers still reading it.
     */
    public ?Money $incomingAmount = null;

    /**
     * @deprecated Not part of the Balance API response; kept for callers still reading it.
     */
    public ?Money $outgoingAmount = null;

    public ?string $transferFrequency = null;

    public ?Money $transferThreshold = null;

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
