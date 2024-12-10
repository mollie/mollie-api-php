<?php

namespace Mollie\Api\Http\Payload;

use DateTimeInterface;

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

    public ?DateTimeInterface $startDate;

    public function __construct(
        string $interval,
        ?string $description = null,
        ?Money $amount = null,
        ?int $times = null,
        ?DateTimeInterface $startDate = null
    ) {
        $this->interval = $interval;
        $this->description = $description;
        $this->amount = $amount;
        $this->times = $times;
        $this->startDate = $startDate;
    }

    public function toArray(): array
    {
        return [
            'interval' => $this->interval,
            'description' => $this->description,
            'amount' => $this->amount,
            'times' => $this->times,
            'startDate' => $this->startDate->format('Y-m-d'),
        ];
    }
}
