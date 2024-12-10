<?php

namespace Mollie\Api\Http\Query;

use DateTimeInterface;
use Mollie\Api\Contracts\Arrayable;

class GetBalanceReportQuery implements Arrayable
{
    public DateTimeInterface $from;

    public DateTimeInterface $until;

    public ?string $grouping;

    public function __construct(
        DateTimeInterface $from,
        DateTimeInterface $until,
        ?string $grouping = null
    ) {
        $this->from = $from;
        $this->until = $until;
        $this->grouping = $grouping;
    }

    public function toArray(): array
    {
        return [
            'from' => $this->from->format('Y-m-d'),
            'until' => $this->until->format('Y-m-d'),
            'grouping' => $this->grouping,
        ];
    }
}
