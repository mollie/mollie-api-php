<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use DateTimeInterface;
use Mollie\Api\Contracts\Resolvable;

readonly class RecurringBillingCycle implements Resolvable
{
    public function __construct(
        /**
         * Possible values: XX months XX weeks XX days.
         *
         * @example: "12 months", "52 weeks" or "365 days".
         */
        public string $interval,
        public ?string $description = null,
        public ?Money $amount = null,
        public ?int $times = null,
        public ?DateTimeInterface $startDate = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'interval' => $this->interval,
            'description' => $this->description,
            'amount' => $this->amount,
            'times' => $this->times,
            'startDate' => $this->startDate,
        ];
    }
}
