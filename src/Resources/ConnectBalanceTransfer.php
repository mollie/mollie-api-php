<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Traits\HasMode;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class ConnectBalanceTransfer extends BaseResource
{
    use HasMode;

    /**
     * Indicates the response contains a connect balance transfer object.
     * Will always contain the string "connect-balance-transfer" for this endpoint.
     *
     * @var string
     */
    public $resource;

    /**
     * The identifier uniquely referring to this connect balance transfer.
     *
     * @example cbt_4KgGJJSZpH
     *
     * @var string
     */
    public $id;

    /**
     * The amount that is transferred from the source balance to the destination balance.
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * The source balance from which the amount will be transferred.
     *
     * @var \stdClass
     */
    public $source;

    /**
     * The destination balance to which the amount will be transferred.
     *
     * @var \stdClass
     */
    public $destination;

    /**
     * A short description of the balance transfer.
     *
     * @var string
     */
    public $description;

    /**
     * The status of the transfer.
     * Possible values: "created", "failed", "succeeded"
     *
     * @var string
     */
    public $status;

    /**
     * The reason for the current status of the transfer, if applicable.
     *
     * @var \stdClass|null
     */
    public $statusReason;

    /**
     * The type of the transfer. Different fees may apply to different types of transfers.
     * Possible values: "invoice_collection", "purchase", "chargeback", "refund",
     * "service_penalty", "discount_compensation", "manual_correction", "other_fee"
     *
     * @var string
     */
    public $category;

    /**
     * @var \stdClass|null
     */
    public $metadata;

    /**
     * The date and time when the transfer was completed, in ISO 8601 format.
     * This parameter is omitted if the transfer is not executed (yet).
     *
     * @var string|null
     */
    public $executedAt;

    /**
     * UTC datetime the connect balance transfer was created in ISO-8601 format.
     *
     * @example "2023-12-25T10:30:54+00:00"
     *
     * @var string
     */
    public $createdAt;
}
