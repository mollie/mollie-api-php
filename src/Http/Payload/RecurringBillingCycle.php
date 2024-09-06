<?php

namespace Mollie\Api\Http\Payload;

use DateTime;
use Mollie\Api\Rules\Matches;

class RecurringBillingCycle extends DataBag
{
    /**
     * Possible values: XX months XX weeks XX days.
     *
     * @example: "12 months", "52 weeks" or "365 days".
     */
    public string $interval;

    public ?string $description;

    public ?Money $amount;

    public ?int $times;

    public ?DateTime $startDate;

    public function __construct(
        string $interval,
        ?string $description = null,
        ?Money $amount = null,
        ?int $times = null,
        ?DateTime $startDate = null,
    ) {
        $this->interval = $interval;
        $this->description = $description;
        $this->amount = $amount;
        $this->times = $times;
        $this->startDate = $startDate;
    }

    public function data(): array
    {
        return [
            'interval' => $this->interval,
            'description' => $this->description,
            'amount' => $this->amount,
            'times' => $this->times,
            'startDate' => $this->startDate->format('Y-m-d'),
        ];
    }

    public function rules(): array
    {
        return [
            'interval' => Matches::pattern('/^\d+ (months|weeks|days)$/'),
        ];
    }
}
