<?php

namespace Mollie\Api\Factories;

use DateTime;
use Mollie\Api\Http\Query\GetBalanceReportQuery;

class GetBalanceReportQueryFactory extends Factory
{
    public function create(): GetBalanceReportQuery
    {
        if (! $this->has(['from', 'unitl'])) {
            throw new \InvalidArgumentException('The "from" and "until" fields are required.');
        }

        return new GetBalanceReportQuery(
            DateTime::createFromFormat('Y-m-d', $this->get('from')),
            DateTime::createFromFormat('Y-m-d', $this->get('until')),
            $this->get('grouping'),
            $this->get('testmode')
        );
    }
}
