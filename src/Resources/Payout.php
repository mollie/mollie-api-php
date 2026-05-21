<?php

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Payout extends BaseResource
{
    /**
     * The identifier uniquely referring to this payout.
     *
     * @example po_4KgGJJSZpH
     *
     * @var string
     */
    public $id;

    /**
     * Mode of the payout, either "live" or "test" depending on the API key that was used.
     *
     * @var string
     */
    public $mode;

    /**
     * Whether this payout was created in test mode.
     *
     * @var bool
     */
    public $testmode;

    /**
     * The identifier of the balance that will be paid out.
     *
     * @example bal_gVMhHKqSSRYJyPsuoPNFH
     *
     * @var string
     */
    public $balanceId;

    /**
     * The amount paid out, excluding any applicable fees.
     *
     * @var \stdClass|null
     */
    public $amount;

    /**
     * The description that will appear on the bank statement for this payout.
     *
     * @var string|null
     */
    public $description;

    /**
     * The status of the payout.
     * Possible values: "requested", "initiated", "processing-at-bank", "completed", "canceled", "failed"
     *
     * @var string
     */
    public $status;

    /**
     * The reason for the status of the payout.
     *
     * @var object|null
     */
    public $statusReason;

    /**
     * UTC datetime the payout was created in ISO-8601 format.
     *
     * @example "2026-05-19T10:30:54+00:00"
     *
     * @var string
     */
    public $createdAt;

    /**
     * UTC datetime the payout was initiated in ISO-8601 format.
     *
     * @example "2026-05-19T10:30:54+00:00"
     *
     * @var string|null
     */
    public $initiatedAt;

    /**
     * UTC datetime the payout was completed in ISO-8601 format.
     *
     * @example "2026-05-19T10:30:54+00:00"
     *
     * @var string|null
     */
    public $completedAt;

    /**
     * UTC datetime the payout was canceled in ISO-8601 format.
     *
     * @example "2026-05-19T10:30:54+00:00"
     *
     * @var string|null
     */
    public $canceledAt;

    /**
     * @var \stdClass
     */
    public $_links;
}
