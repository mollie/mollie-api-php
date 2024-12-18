<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Data\GetBalanceReportQuery;

class GetBalanceReportQueryFactory extends Factory
{
    public function create(): GetBalanceReportQuery
    {
        if (! $this->has(['from', 'until'])) {
            throw new \InvalidArgumentException('The "from" and "until" fields are required.');
        }

        return new GetBalanceReportQuery(
            DateTimeImmutable::createFromFormat('Y-m-d', $this->get('from')),
            DateTimeImmutable::createFromFormat('Y-m-d', $this->get('until')),
            $this->get('grouping'),
        );
    }
}
