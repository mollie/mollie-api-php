<?php

namespace Mollie\Api\Http\Query;

use DateTimeInterface;

class GetBalanceReportQuery extends Query
{
    public DateTimeInterface $from;

    public DateTimeInterface $until;

    public ?string $grouping;

    public ?bool $testmode;

    public function __construct(
        DateTimeInterface $from,
        DateTimeInterface $until,
        ?string $grouping = null,
        ?bool $testmode = null
    ) {
        $this->from = $from;
        $this->until = $until;
        $this->grouping = $grouping;
        $this->testmode = $testmode;
    }

    public function toArray(): array
    {
        return [
            'from' => $this->from->format('Y-m-d'),
            'until' => $this->until->format('Y-m-d'),
            'grouping' => $this->grouping,
            'testmode' => $this->testmode,
        ];
    }
}
