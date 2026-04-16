<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Traits\HasMode;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class ConnectBalanceTransfer extends BaseResource
{
    use HasMode;


    public string $id;

    public Money $amount;

    /**
     * @var \stdClass
     */
    public $source;

    /**
     * @var \stdClass
     */
    public $destination;

    public string $description;

    public string $status;

    /**
     * @var \stdClass|null
     */
    public $statusReason;

    public string $category;

    /**
     * @var \stdClass|null
     */
    public $metadata;

    public ?string $executedAt = null;

    public string $createdAt;
}
