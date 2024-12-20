<?php

namespace Mollie\Api\Http\Data;

use DateTimeInterface;
use Mollie\Api\Contracts\Resolvable;

class GetBalanceReportQuery implements Resolvable
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
            'from' => $this->from,
            'until' => $this->until,
            'grouping' => $this->grouping,
        ];
    }
}
