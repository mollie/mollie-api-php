<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Traits\HasMode;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class BalanceTransaction extends BaseResource
{
    use HasMode;


    public string $mode;

    public string $id;

    public string $type;

    public string $createdAt;

    public Money $resultAmount;

    public Money $initialAmount;

    public Money $deductions;

    /**
     * @var \stdClass
     */
    public $context;
}
