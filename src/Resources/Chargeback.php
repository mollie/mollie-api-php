<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Chargeback extends BaseResource
{
    public string $id;

    /**
     * The amount that was charged back.
     */
    public Money $amount;

    /**
     * UTC datetime the chargeback was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     */
    public ?string $createdAt = null;

    /**
     * The payment id that was charged back.
     */
    public string $paymentId;

    /**
     * The settlement amount.
     */
    public ?Money $settlementAmount = null;

    /**
     * The identifier referring to the settlement this payment was settled with.
     */
    public ?string $settlementId = null;

    /**
     * The chargeback reason.
     *
     * @var \stdClass|null
     */
    public $reason;

    /**
     * UTC datetime the chargeback was reversed in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     */
    public ?string $reversedAt = null;

    /**
     * @var \stdClass|null
     */
    public $_links;
}
