<?php

namespace Mollie\Api\Http\Query;

use DateTime;

class GetBalanceReportQuery extends Query
{
    public DateTime $from;

    public DateTime $until;

    public ?string $grouping;

    public ?bool $testmode;

    public function __construct(
        DateTime $from,
        DateTime $until,
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
