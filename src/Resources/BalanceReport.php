<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class BalanceReport extends BaseResource
{

    public string $balanceId;

    public string $timeZone;

    public string $from;

    public string $until;

    public string $grouping;

    /**
     * @var \stdClass
     */
    public $totals;

    /**
     * @var \stdClass
     */
    public $_links;
}
